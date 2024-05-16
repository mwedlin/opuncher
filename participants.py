#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Creates a csv-file with participant codes.
# 1: Length of code (12)
# 2: Number of participant codes to generate (1000)
# 3: cvs-file (none)
# 4: sql-file that puts the participants in the database (none)
# 5: Game number (1)

import random, itertools, os, sys

conso = ["b","c","d","f","g","h","j","k","m","n","p","r","s","t","v","w","x","y","z"]
numeric = ["2","3","4","5","6","7","8","9"]
vocal = ["a","e","i","o","u"]

def main(argv):
    try:
        length = int(argv[1])
    except IndexError:
        length = 12
    try:
        no_of_pwd = int(argv[2])
    except IndexError:
        no_of_pwd = 1000
    try:
        f_cvs = open(argv[3], 'w')
    except IndexError:
        f_cvs = None
    try:
        f_sql = open(argv[4], 'w')
    except IndexError:
        f_sql = None
    try:
        game_no = int(argv[5])
    except IndexError:
        game_no = 1

    # Calculate max number of passwords
    if (length % 2) == 1:
        templength = length -1
    else:
        templength = length -2
    max = int(templength / 2)
    numpwd = len(conso)**max + len(vocal)**max + len(numeric)**2
    strength = 4.7*(max+4) + 2*3.32
    
    print("Max number of different codes: ", numpwd, " Strength: ", strength)
    
    pwdarray = []
    
    for i in range(no_of_pwd):
        password = generate_password(length)
        while password in pwdarray:
            print("Password ", i, "is a duplicate")
            password = generate_password(length)
        pwdarray.append(password)

    if f_cvs != None:
        for i in range(len(pwdarray)):
            f_cvs.write(str(i+1) + ',' + pwdarray[i] + '\n')

    if f_sql != None:
        f_sql.write("""
use foxy;
/* Delete old tickets. */
DELETE FROM users WHERE game=""" + str(game_no) + """;
/* Default tickets */
INSERT INTO users (id, ident, game, name, full_name) VALUES (1000, "tick1", 1, "adm", "Administrator");
/* All others */
""")
    for i in range(len(pwdarray)):
        f_sql.write('INSERT INTO users (id, ident, game, name, full_name) VALUES (' + str(i+1) + ', "' +  pwdarray[i] + '", "' +  str(game_no) + '", "' +  str(i+1) + '", "Deltagare ' + str(i+1) + '");\n')
    
    return 0

def usage(argv0):
    p = sys.stderr.write
    p("Usage: %s length number_of_passwords [cvsfile] [sqlfile]\n" % argv0)
    return 1

def generate_password(length):
    global conso, vocal,  numeric
    
    if (length % 2) == 1:
        templength = length -1
    else:
        templength = length -2
    max = int(templength / 2)

    password = ""
    for i in range(int(max/2)):
        password += random.choice(conso) + random.choice(vocal)
    password += random.choice(numeric) + random.choice(numeric)
    for i in range(int(max/2)):
        password += random.choice(conso) + random.choice(vocal)

    return(password)

if __name__ == '__main__':
    sys.exit(main(sys.argv))

