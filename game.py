#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Creates files for generation of the admin QR codes.
# 1: Length of code (12)
# 2: Number of controls (3)
# 3: csv-file
# 4: sql-file
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
        no_of_tran = int(argv[2])
    except IndexError:
        no_of_tran = 3
    try:
        f_csv = open(argv[3], 'w')
    except IndexError:
        f_csv = None
    try:
        f_sql = open(argv[4], 'w')
    except IndexError:
        f_sql = None
    try:
        game_no = int(argv[5])
    except IndexError:
        game_no = 1
    try:
        url_base = argv[6]
    except IndexError:
        url_base = "https://example.com"

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
    
    for i in range(no_of_tran):
        password = generate_password(length)
        while password in pwdarray:
            print("Password is a duplicate")
            password = generate_password(length)
        pwdarray.append(password)

    if f_csv != None:
        f_csv.write(
            'Start,' + url_base + '/start.php\nEnd,' + url_base + '/finish.php\n'
            )
        
        for i in range(len(pwdarray)):
            f_csv.write('Control ' + str(i+1) + ',' + url_base + '/punch.php?code=' + pwdarray[i] + '\n')

    if f_sql != None:
        f_sql.write("""
use foxy;
/* Delete old transmitters. */
DELETE FROM transmitters;
""")
        for i in range(len(pwdarray)):
            f_sql.write('INSERT INTO transmitters (game, id, name, code) VALUES (' + str(game_no) + ', ' + str(i+1) + ', "Control ' + str(i+1) + '", "' + pwdarray[i] + '");\n')
      
        return 0

def usage(argv0):
    p = sys.stderr.write
    p("Usage: %s length number_of_passwords [csvfile] [sqlfile] [game number]\n" % argv0)
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

