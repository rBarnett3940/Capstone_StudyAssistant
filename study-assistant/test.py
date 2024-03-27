from flask import Flask, session

app = Flask(__name__)
app.secret_key = 'super_secret_key'

# List of courses
courses = ['COMP1126', 'COMP1127']

# Define the route function
def route_function(course):
    # Set session data for the course
    session['current_course'] = course
    return f"You are now on the {course} route."

# Define routes dynamically for each course
for course in courses:
    # Assign the route function to the app with a unique name
    app.route(f"/{course}_route")(route_function(course))

if __name__ == "__main__":
    app.run(debug=True)





