import numpy as np
from flask import Flask, request, jsonify, render_template
import pickle
import math
import mysql.connector
from flask_cors import CORS

# Create Flask App
app = Flask(__name__)
CORS(app, origins='http://localhost')

# Course list
courses = ["COMP1126", "COMP1127", "COMP1161", "COMP1210", "COMP1220", "COMP2190", "COMP2140", "COMP2171", "COMP2201", "COMP2211", "COMP2340", "COMP2130"]

# Route for COMP1126
@app.route("/COMP1126_ai", methods=["POST"])
def predict_COMP1126():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root", 
            password="",  
            database="capstone"  
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1126 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP1126 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1126) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        response_data = {"message": "For COMP1126 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)


# Route for COMP1127
@app.route("/COMP1127_ai", methods=["POST"])
def predict_COMP1127():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[1]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[1]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root", 
            password="", 
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1127 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP1127 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1127) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP1127 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP1161
@app.route("/COMP1161_ai", methods=["POST"])
def predict_COMP1161():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")

    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[2]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[2]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",  
            password="", 
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1161 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP1161 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1161) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP1161 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP1210
@app.route("/COMP1210_ai", methods=["POST"])
def predict_COMP1210():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[3]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[3]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",
            password="",
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1210 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP1210 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1210) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP1210 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP1220
@app.route("/COMP1220_ai", methods=["POST"])
def predict_COMP1220():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[4]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[4]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)

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
            user="root",
            password="", 
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP1220 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP1220 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP1220) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP1220 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2190
@app.route("/COMP2190_ai", methods=["POST"])
def predict_COMP2190():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[5]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[5]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root", 
            password="",  
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2190 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2190 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2190) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2190 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2140
@app.route("/COMP2140_ai", methods=["POST"])
def predict_COMP2140():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[6]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[6]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",
            password="",
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2140 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2140 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2140) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2140 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2171
@app.route("/COMP2171_ai", methods=["POST"])
def predict_COMP2171():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[7]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[7]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",
            password="",
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2171 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2171 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2171) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2171 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2201
@app.route("/COMP2201_ai", methods=["POST"])
def predict_COMP2201():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[8]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[8]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",
            password="",
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2201 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2201 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2201) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2201 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2211
@app.route("/COMP2211_ai", methods=["POST"])
def predict_COMP2211():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[9]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[9]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",
            password="",
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2211 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2211 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2211) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2211 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2340
@app.route("/COMP2340_ai", methods=["POST"])
def predict_COMP2340():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")

    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[10]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[10]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root",
            password="",
            database="capstone" 
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2340 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2340 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2340) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2340 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)
    

# Route for COMP2130
@app.route("/COMP2130_ai", methods=["POST"])
def predict_COMP2130():
    # Get form data
    data = request.form
    Mode = data.get("Mode")
    Performance = data.get("Performance")
    Difficulty = data.get("Difficulty")
    Courses = data.get("Courses")
    Retention = data.get("Retention")
    user = data.get("User")


    # Load the pickle model
    model = pickle.load(open(f"./pkl/{courses[11]}_model.pkl", "rb"))
    scaler = pickle.load(open(f"./pkl/{courses[11]}_scaler.pkl", "rb"))

    # Check if any of the form fields is empty
    if not all([Mode, Performance, Difficulty, Courses, Retention]):
        response_data = {"message": "Error! Please ensure you enter all values!", "prediction": 0}
        return jsonify(response_data)
    
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
            user="root", 
            password="", 
            database="capstone"
        )

        # Create a cursor object to execute SQL queries
        cursor = conn.cursor()

        cursor.execute(f"SELECT COMP2130 FROM hours WHERE userID = %s", (user,))
        existing_prediction = cursor.fetchone()
        print(cursor)

        if existing_prediction:
            # Update existing prediction
            cursor.execute(f"UPDATE hours SET COMP2130 = %s WHERE userID = %s", (rounded_prediction, user))
            conn.commit()
        else:
            # Insert the new prediction
            cursor.execute(f"INSERT INTO hours (userID, COMP2130) VALUES (%s, %s)", (user, rounded_prediction))
            conn.commit()

        # Close the cursor and connection
        cursor.close()
        conn.close()
        
        response_data = {"message": "For COMP2130 the recommeded amount of hours you should study is:", "prediction": rounded_prediction}
        return jsonify(response_data)
    except mysql.connector.Error as e:
        # Handle database connection errors
        error_message = f"Failed to connect to the database: {e}"
        print(error_message)
        response_data = {"message": "Error Predicting value. Please try again later!", "prediction": 0}
        return jsonify(response_data)


if __name__ == "__main__":
    app.run(debug=True)
