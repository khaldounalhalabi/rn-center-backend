<?php

namespace App\Modules\DICOM;

use Illuminate\Support\Facades\URL;

/**
 * DICOM Mode Extractor - Extracts modalities from study series and determines applicable view modes
 * This function replicates OHIF's logic for determining which viewing modes are valid
 * for a DICOM study by analyzing all series within the study.
 */
class DicomModeValidator
{
    const BASIC_MODE = 'basic';
    const MICROSCOPY_MODE = 'microscopy';
    const SEGMENTATION_MODE = 'segmentation';
    const LONGITUDINAL_MODE = 'longitudinal';
    const TMTV_MODE = 'tmtv';
    const PRECLINICAL_4D_MODE = 'preclinical-4d';

    /**
     * Extract modalities from study series and determine applicable view modes
     * @param array $studySeries Study data from Orthanc containing series information
     * @return array Array with modalities and valid modes
     */
    public static function extractModalitiesAndViewModes(array $studySeries, array $study): array
    {
        // Initialize modalities array
        $modalities = [];

        // Check if the study has series data
        if (!count($studySeries)) {
            return [
                'modalities' => [],
                'modalitiesString' => '',
                'validModes' => [],
                'error' => 'No series data found in study'
            ];
        }

        // Loop through all series to extract modalities
        foreach ($studySeries as $series) {
            if (isset($series['MainDicomTags']['Modality'])) {
                $modality = $series['MainDicomTags']['Modality'];
                if (!in_array($modality, $modalities)) {
                    $modalities[] = $modality;
                }
            }
        }

        // Create a modality string (like OHIF does)
        $modalitiesString = implode('\\', $modalities);

        // Determine valid modes based on extracted modalities
        $validModes = self::determineValidModes($modalities, $study);

        return [
            'modalities' => $modalities,
            'modalitiesString' => $modalitiesString,
            'validModes' => $validModes,
            'totalSeries' => count($studySeries),
            'totalModalities' => count($modalities)
        ];
    }

    /**
     * Determine valid view modes based on modalities and study metadata
     * @param array $modalities Array of modalities found in the study
     * @param array $study      Study metadata
     * @return array Array of valid modes
     */
    private static function determineValidModes(array $modalities, array $study): array
    {
        $validModes = [];
        $modalitiesList = $modalities;

        // Get study metadata
        $mrn = $study['ParentPatient'] ?? null;
        $studyInstanceUid = $study['MainDicomTags']['StudyInstanceUID'] ?? null;

        // 1. Basic Viewer Mode
        // Excludes non-image modalities
        $nonImageModalities = ['ECG', 'SR', 'SEG', 'RTSTRUCT', 'RTPLAN', 'PR', 'DOC'];
        $validModalities = array_filter($modalitiesList, function ($modality) use ($nonImageModalities) {
            return !in_array($modality, $nonImageModalities);
        });

        if (!empty($validModalities)) {
            $validModes[self::BASIC_MODE] = [
                'id' => self::BASIC_MODE,
                'displayName' => 'Basic Viewer',
                'description' => 'Basic DICOM viewer for standard imaging modalities',
                'valid' => true,
                'url' => URL::format(config('orthanc.ohif_url'), self::modeURLMatch(self::BASIC_MODE) . "?StudyInstanceUIDs=" . $studyInstanceUid)
            ];
        }

        // 2. Microscopy Mode
        // Only supports SM modality
        if (in_array('SM', $modalitiesList)) {
            $validModes[self::MICROSCOPY_MODE] = [
                'id' => self::MICROSCOPY_MODE,
                'displayName' => 'Microscopy',
                'description' => 'Microscopy mode for SM modality',
                'valid' => true,
                'url' => URL::format(config('orthanc.ohif_url'), self::modeURLMatch(self::MICROSCOPY_MODE) . "?StudyInstanceUIDs=" . $studyInstanceUid)
            ];
        }

        // 3. Segmentation Mode
        //  doesn't show if the study has only one modality that is unsupported
        $unsupportedForSegmentation = ['SM', 'ECG', 'OT', 'DOC'];
        $validForSegmentation = !(count($modalitiesList) === 1) || !in_array($modalitiesList[0], $unsupportedForSegmentation);

        if ($validForSegmentation) {
            $validModes[self::SEGMENTATION_MODE] = [
                'id' => self::SEGMENTATION_MODE,
                'displayName' => 'Segmentation',
                'description' => 'Segmentation tools for imaging studies',
                'valid' => true,
                'url' => URL::format(config('orthanc.ohif_url'), self::modeURLMatch(self::SEGMENTATION_MODE) . "?StudyInstanceUIDs=" . $studyInstanceUid)
            ];
        }

        // 4. Longitudinal Mode
        // Excludes non-image modalities
        $longitudinalNonImageModalities = ['ECG', 'SEG', 'RTSTRUCT', 'RTPLAN', 'PR'];
        $validForLongitudinal = array_filter($modalitiesList, function ($modality) use ($longitudinalNonImageModalities) {
            return !in_array($modality, $longitudinalNonImageModalities);
        });

        if (!empty($validForLongitudinal)) {
            $validModes[self::LONGITUDINAL_MODE] = [
                'id' => self::LONGITUDINAL_MODE,
                'displayName' => 'Longitudinal',
                'description' => 'Longitudinal study comparison',
                'valid' => true,
                'url' => URL::format(config('orthanc.ohif_url'), self::modeURLMatch(self::LONGITUDINAL_MODE) . "?StudyInstanceUIDs=" . $studyInstanceUid)
            ];
        }

        // 5. TMTV Mode (Total Metabolic Tumor Volume)
        // Requires both CT and PT, excludes certain conditions
        $hasCT = in_array('CT', $modalitiesList);
        $hasPT = in_array('PT', $modalitiesList);
        $excludedMRN = $mrn === 'M1';
        $excludedStudyUID = $studyInstanceUid === '1.3.6.1.4.1.12842.1.1.14.3.20220915.105557.468.2963630849';
        $hasInvalidModality = in_array('SM', $modalitiesList);

        if ($hasCT && $hasPT && !$excludedMRN && !$excludedStudyUID && !$hasInvalidModality) {
            $validModes[self::TMTV_MODE] = [
                'id' => self::TMTV_MODE,
                'displayName' => 'TMTV',
                'description' => 'Total Metabolic Tumor Volume analysis',
                'valid' => true,
                'url' => URL::format(config('orthanc.ohif_url'), self::modeURLMatch(self::TMTV_MODE) . "?StudyInstanceUIDs=" . $studyInstanceUid)
            ];
        }

        // 6. Preclinical 4D Mode
        // Only available for studies with MRN = M1
        if ($mrn === 'M1') {
            $validModes[self::PRECLINICAL_4D_MODE] = [
                'id' => self::PRECLINICAL_4D_MODE,
                'displayName' => 'Preclinical 4D',
                'description' => '4D PET/CT analysis for preclinical studies',
                'valid' => true,
                'url' => URL::format(config('orthanc.ohif_url'), self::modeURLMatch(self::PRECLINICAL_4D_MODE) . "?StudyInstanceUIDs=" . $studyInstanceUid)
            ];
        }

        return $validModes;
    }

    /**
     * @param string $mode
     * @return string
     */
    public static function modeURLMatch(string $mode): string
    {
        return match ($mode) {
            self::SEGMENTATION_MODE => "segmentation",
            self::PRECLINICAL_4D_MODE => "dynamic-volume",
            self::TMTV_MODE => "tmtv",
            self::MICROSCOPY_MODE => "microscopy",
            default => "viewer",
        };
    }
}
