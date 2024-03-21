import numpy as np
from flask import Flask, request, jsonify, render_template
import pickle
import math
import mysql.connector

# Create Flask App
app = Flask(__name__)

# Load the pickle model
model = pickle.load(open("linear_regression_model.pkl", "rb"))
scaler = pickle.load(open("scaler.pkl", "rb"))

# Define the home page
@app.route("/")
def home():
    course_text = "COMP1126 - Introduction to Computing I"
    return render_template("index.html", course_text=course_text)

    

@app.route("/", methods=["POST"])
def predict():
    COMP1126_Performance = request.form.get("COMP1126_Performance")
    COMP1126_Difficulty = request.form.get("COMP1126_Difficulty")
    COMP1126_Courses = request.form.get("COMP1126_Courses")

    # Check if any of the form fields is empty
    if not all([COMP1126_Performance, COMP1126_Difficulty, COMP1126_Courses]):
        return render_template("index.html", prediction_text="Please fill in all the required fields", course_text="COMP1126 - Introduction to Computing I")

    # Convert form values to integers
    COMP1126_Performance = int(COMP1126_Performance)
    COMP1126_Difficulty = int(COMP1126_Difficulty)
    COMP1126_Courses = int(COMP1126_Courses)
    
    # Create a feature array with the retrieved values
    features = np.array([[COMP1126_Performance, COMP1126_Difficulty, COMP1126_Courses]])
    
    # Standardize the features using the scaler
    features_scaled = scaler.transform(features)
    
    # Make predictions
    prediction = model.predict(features_scaled)
    rounded_prediction = math.ceil(prediction[0])

    # Connect to the MySQL database
    conn = mysql.connector.connect(
        host="localhost",
        user="admin2",  # Update with your MySQL username
        password="",  # Update with your MySQL password
        database="timetable"  # Update with your MySQL database name
    )

    # Create a cursor object to execute SQL queries
    cursor = conn.cursor()

    cursor.execute("SELECT comp1126 FROM hours WHERE userID = %s", (120,))
    existing_prediction = cursor.fetchone()
    print(cursor)

    if existing_prediction:
        # Handle existing prediction (e.g., update or ignore)
        cursor.execute("UPDATE hours SET comp1126 = %s WHERE userID = %s", (rounded_prediction, 120))
        conn.commit()
    else:
        # Insert the new prediction
        cursor.execute("INSERT INTO hours (userID, comp1126) VALUES (%s, %s)", (120, rounded_prediction))
        conn.commit()

    # Close the cursor and connection
    cursor.close()
    conn.close()
    
    return render_template("index.html", prediction_text="The number of hours is {}".format(rounded_prediction), course_text="COMP1126 - Introduction to Computing I")

if __name__ == "__main__":
    app.run(debug=True)
