import mysql.connector

mydb = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="mydatabase"
)

mycursor = mydb.cursor()

for i in range(1, 179):
    email = f"me24b{i:03}@smail.iitm.ac.in"

    sql = "INSERT INTO allowed_emails (email) VALUES (%s)"
    val = (email,)
    mycursor.execute(sql, val)

mydb.commit()

print(mycursor.rowcount, "record(s) inserted.")

mycursor.close()
mydb.close()
