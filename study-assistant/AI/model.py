import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import mean_squared_error
from sklearn.metrics import accuracy_score
import pickle
import numpy as np

# Load the dataset
df = pd.read_csv("test_data_2.csv")

# Select relevant features and target variable
df2 = df[['COMP1126_Performance', 'COMP1126_Difficulty', 'COMP1126_Courses', 'COMP1126_Hours']]
df2.dropna(inplace=True)

X = df2[['COMP1126_Performance', 'COMP1126_Difficulty', 'COMP1126_Courses']]
print(X)
y = df2['COMP1126_Hours']
print(y)

# Split the dataset into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Standardize the features
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

print(f"Training MSE: {train_mse}")
print(f"Testing MSE: {test_mse}")


# Make predictions on the test set
y_pred = model.predict(X_test)

# Calculate the mean of the true target variable
baseline_prediction = y_test.mean()

# Compute the MSE for the baseline model
mse_baseline = mean_squared_error(y_test, np.full_like(y_test, baseline_prediction))

# Compute the MSE for your model
mse_model = mean_squared_error(y_test, y_pred)

# Print MSE values
print(f"Baseline MSE: {mse_baseline}")
print(f"Model MSE: {mse_model}")

# Compare the MSE values
if mse_model < mse_baseline:
    print("Model MSE is lower than the baseline MSE. Model performs better.")
elif mse_model > mse_baseline:
    print("Model MSE is higher than the baseline MSE. Model performs worse.")
else:
    print("Model MSE is equal to the baseline MSE. Model performance is comparable to the baseline.")


# Calculate R-squared scores
threshold = 3

# Convert regression predictions to binary classes
y_pred_train_binary = (y_pred_train > threshold).astype(int)
y_pred_test_binary = (y_pred_test > threshold).astype(int)

# Calculate accuracy scores
train_accuracy = accuracy_score((y_train > threshold).astype(int), y_pred_train_binary)
test_accuracy = accuracy_score((y_test > threshold).astype(int), y_pred_test_binary)

print(f"Training Accuracy: {train_accuracy}")
print(f"Testing Accuracy: {test_accuracy}")

# Save the trained model and scaler to files using pickle
with open('linear_regression_model.pkl', 'wb') as model_file:
    pickle.dump(model, model_file)

with open('scaler.pkl', 'wb') as scaler_file:
    pickle.dump(scaler, scaler_file)


if mse_model < mse_baseline:
    print("Model MSE is lower than the baseline MSE. Model performs better.")
elif mse_model > mse_baseline:
    print("Model MSE is higher than the baseline MSE. Model performs worse.")
else:
    print("Model MSE is equal to the baseline MSE. Model performance is comparable to the baseline.")