import numpy as np
from flask import Flask, request, jsonify, render_template
import pickle
import math
import mysql.connector
from flask_cors import CORS

# Create Flask App
app = Flask(__name__)
CORS(app, origins='http://localhost')



# Define the route for the root URL ("/")
"""@app.route("/", methods=["GET", "POST"])
def root():
    if request.method == "POST":
        # Handle POST request
        # You can put your POST request handling logic here
        return predictCOMP1126()
    else:
        # Handle GET request
        # You can put your GET request handling logic here
        pass"""

courses = ['COMP1126', 'COMP1127']

# Define the home page
@app.route("/COMP1126_ai_starter", methods=["POST"])
def homeCOMP1126 ():
    data = request.form
    user = data.get("userID")
    tod = data.get("tod")
    dow = data.get("dow")
    maxHours = data.get("maxHours")
    return render_template("ai_questions.html", title="COMP1126", user=user, tod=tod, dow=dow, maxHours=maxHours)

@app.route("/COMP1126_ai", methods=["POST"])
def predict_COMP1126():
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")
    tod = data.get("Tod")
    dow = data.get("Dow")
    maxHours = data.get("MaxHours")

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        return render_template("ai_questions.html", title="COMP1126", message="Error! Please ensure you enter all values!", user=user, tod=tod, dow=dow, maxHours=maxHours)

    # Convert form values to integers
    Mode = int(Mode)
    Performance = int(Performance)
    Difficulty = int(Difficulty)
    Courses = int(Courses)
    Retention = int(Retention)

    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[0]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[0]}_scaler.pkl", "rb"))
    
    
    # Create a feature array with the retrieved values
    features = np.array([[Mode, Performance, Difficulty, Courses, Retention]])

    # Standardize the features using the scaler
    features_scaled = scaler.transform(features)
    
    # Make predictions
    prediction = model.predict(features_scaled)
    rounded_prediction = math.ceil(prediction[0])
    if rounded_prediction <= 0:
        rounded_prediction = 1

    try:
        # Connect to the MySQL database
        conn = mysql.connector.connect(
            host="localhost",
            user="root",  # Update with your MySQL username
            password="",  # Update with your MySQL password
            database="timetable"  # Update with your MySQL database name
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1126 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Handle existing prediction (e.g., update or ignore)
            cursor.execute(f"UPDATE hours SET COMP1126 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1126) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        return render_template("generated_hours.html", title="COMP1126", message="For COMP1126 the recommeded amount of hours you should study is:", prediction=rounded_prediction, user=user, tod=tod,  dow=dow, maxHours=maxHours)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        return render_template("ai_questions.html", title="COMP1126", message="Error Predicting value. Please try again later!", user=user, tod=tod,  dow=dow, maxHours=maxHours)



# Define the home page
@app.route("/COMP1127_ai_starter", methods=["POST"])
def homeCOMP1127():
    data = request.form
    user = data.get("userID")
    tod = data.get("tod")
    dow = data.get("dow")
    maxHours = data.get("maxHours")
    return render_template("ai_questions.html", title="COMP1127", user=user, tod=tod, dow=dow, maxHours=maxHours)


@app.route("/COMP1127_ai", methods=["POST"])
def predict_COMP1127():
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")
    tod = data.get("Tod")
    dow = data.get("Dow")
    maxHours = data.get("MaxHours")

    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[1]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[1]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        return render_template("ai_questions.html", title="COMP1127", message="Error! Please ensure you enter all values!", user=user, tod=tod, dow=dow, maxHours=maxHours)

    # Convert form values to integers
    Mode = int(Mode)
    Performance = int(Performance)
    Difficulty = int(Difficulty)
    Courses = int(Courses)
    Retention = int(Retention)
    
    # Create a feature array with the retrieved values
    features = np.array([[Mode, Performance, Difficulty, Courses, Retention]])
    
    # Standardize the features using the scaler
    features_scaled = scaler.transform(features)
    
    # Make predictions
    prediction = model.predict(features_scaled)
    rounded_prediction = math.ceil(prediction[0])
    if rounded_prediction <= 0:
        rounded_prediction = 1


    try:
        # Connect to the MySQL database
        conn = mysql.connector.connect(
            host="localhost",
            user="root",  # Update with your MySQL username
            password="",  # Update with your MySQL password
            database="timetable"  # Update with your MySQL database name
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1127 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Handle existing prediction (e.g., update or ignore)
            cursor.execute(f"UPDATE hours SET COMP1127 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1127) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        return render_template("generated_hours.html", title="COMP1127", message="For COMP1127 the recommeded amount of hours you should study is:", prediction=rounded_prediction, user=user, tod=tod, dow=dow, maxHours=maxHours)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        return render_template("ai_questions.html", title="COMP1127", message="Error Predicting value. Please try again later!", user=user, tod=tod, dow=dow, maxHours=maxHours)



if __name__ == "__main__":
    app.run(debug=True)
