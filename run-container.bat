@ECHO OFF

CALL docker build -t rn-center-backend .
CALL docker run -d -p 9000:80 --name rn-center-backend-container rn-center-backend

