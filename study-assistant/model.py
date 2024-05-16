import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import mean_squared_error
from sklearn.metrics import accuracy_score
import pickle
import numpy as np


# Load the dataset
df = pd.read_csv("./csv/output.csv")
courses = ["COMP1126", "COMP1127", "COMP1161", "COMP1210", "COMP1220", "COMP2190", "COMP2140", "COMP2171", "COMP2201", "COMP2211", "COMP2340", "COMP2130"]


# Loop through all courses and get models for each
for course in courses:
    print(f"{course}_Performance")
    # Select relevant features and target variable
    df2 = df[['Mode', f'{course}_Performance', f'{course}_Difficulty', f'{course}_Courses', f'{course}_Hours', 'Retention']]
    df2.dropna(inplace=True)

    X = df2[['Mode', f'{course}_Performance', f'{course}_Difficulty', f'{course}_Courses', 'Retention']]
    y = df2[f'{course}_Hours']

    # Split the dataset into training and testing sets
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # Standardize features
    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    # Initialize and train the linear regression model
    model = LinearRegression()
    model.fit(X_train_scaled, y_train)

    # Make predictions
    y_pred_train = model.predict(X_train_scaled)
    y_pred_test = model.predict(X_test_scaled)

    # Evaluate the model
    train_mse = mean_squared_error(y_train, y_pred_train)
    test_mse = mean_squared_error(y_test, y_pred_test)

    print(f"{course} Training MSE: {train_mse}")
    print(f"{course} Testing MSE: {test_mse}")


    # Save the trained model and scaler to files using pickle to be used by flask 
    with open(f'./pkl/{course}_model.pkl', 'wb') as model_file:
        pickle.dump(model, model_file)

    with open(f'./pkl/{course}_scaler.pkl', 'wb') as scaler_file:
        pickle.dump(scaler, scaler_file)