# Reslan Alnaal Medical Center Management System (Graduation Project)

## Introduction 
This is the backend of the project made to seize the bachelor's degree in software engineering at Damascus University.
The project is a medical center management system which provides a platform for the management of the center's staff and patients.

## Prerequisites
- docker
- MySQL
- Git
- Orthanc Server
- Node.js
- OHIF fork from https://github.com/khaldounalhalabi/OHIF-Fork

## Technologies
- PHP/Laravel
- MySQL
- ANTLR 4
- Next.js
- Tailwind CSS
- Shadcn
- React Native
- Expo

## Installation and running
1. run `php artisan jwt:secret`
2. run `php artisan key:generate`
3. run the following commands:
```bash
docker build -t rn-center-backend .
docker run -d -p 9000:80 --name rn-center-backend-container rn-center-backend
```

now you can access the backend at http://localhost:9000

## Next Steps
- Clone And Run The Frontend Project [Frontend Repository URL](https://github.com/khaldounalhalabi/rn-center-frontend) 
- Clone And Run The OHIF Fork [OHIF Fork Repository URL](https://github.com/khaldounalhalabi/OHIF-Fork)
- Clone And Run The Employee Mobile App [Employee Mobile App Repository URL](https://github.com/khaldounalhalabi/rn-center-employee-mobile-app)
- Clone And Run The Patient Mobile App [Patient Mobile App Repository URL](https://github.com/khaldounalhalabi/rn-center-patient-mobile-app)
