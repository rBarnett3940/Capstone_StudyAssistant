# Python automation script to convert csv text values to numbers
import csv


def check(cell):
    match cell:
        case "Alone":
            return 1
        case "In a group":
            return 2
        case "Morning":
            return 1
        case "Afternoon":
            return 2
        case "Evening":
            return 3
        case "No preference":
            return 4
        case "Quiet environment":
            return 1
        case "Background noise":
            return 2
        case "Flashcards":
            return 1
        case "Study Groups":
            return 2
        case "Online Resources":
            return 3
        case "Yes":
            return 1
        case "No":
            return 0
        case "Poor":
            return 1
        case "Fair":
            return 2
        case "Good":
            return 3
        case "Excellent":
            return 4
        case "During the Examination Period":
            return 0
        case "NA":
            return 6
        case default:
            return cell


# Define the file paths
input_file = 'test_data_2.csv'
output_file = 'output.csv'

# Read data from input CSV file and write to output CSV file with desired changes
with open(input_file, 'r', newline='') as infile, open(output_file, 'w', newline='') as outfile:
    reader = csv.reader(infile)
    writer = csv.writer(outfile)


    # Run the check function for each cell
    for row in reader:
        data = []
        for cell in row:
            num = check(cell)
            
            data.append(num)
        writer.writerow(data)

print("CSV file processing complete.")



