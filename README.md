# Capstone_StudyAssistant

npm install -g nodemon

python -m venv venv
.\venv\Scripts\activate
pip install -r requirements.txt

cd study-assistant
Flask run --debug
nodemon server.js


pip freeze > requirements.txt
