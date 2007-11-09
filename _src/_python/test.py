#
# EtoA User Statistics calculation
#
# Author: mrcage <mrcage@etoa.ch>
# Last Changes: 12.7.2007
# Command-Line: python stats.py
#

# Import required modules
import time
import MySQLdb
import MySQLdb.cursors

t = time.time();

# Connect to database
db = MySQLdb.connect(host="localhost", user="etoa", passwd="ad.OB2Asd_ad48*a4zjq-kzf6+",db="etoaround1",)
#db = MySQLdb.connect(host="localhost", user="etoadev", passwd="489ar_dfBDfgs4aA18ff_aniuAadad",db="etoatest")
cursor = db.cursor(MySQLdb.cursors.SSCursor)

# Load ships
ships = {}
cursor.execute("SELECT ship_id,ship_battlepoints FROM	ships;")
result = cursor.fetchall()
for record in result:
	ships[record[0]] = record[1]

# Load defense
defense = {}
cursor.execute("SELECT def_id,def_battlepoints FROM	defense;")
result = cursor.fetchall()
for record in result:
	defense[record[0]] = record[1]

# Load buildings
buildings = {}
cursor.execute("SELECT bp_building_id,bp_level,bp_points FROM	building_points;")
result = cursor.fetchall()
for record in result:
	buildings[str(record[0])+":"+str(record[1])] = record[2]

# Load techs
techs = {}
cursor.execute("SELECT bp_tech_id,bp_level,bp_points FROM	tech_points;")
result = cursor.fetchall()
for record in result:
	techs[str(record[0])+":"+str(record[1])] = record[2]

print round(time.time() - t,3)


# Check every user
udata = []
cursor.execute("SELECT user_id,user_nick FROM	users;")
ures = cursor.fetchall()
for uarr in ures:
	
	# Reset counters
	user_id = str(uarr[0])
	points = 0;
	points_ships = 0;
	points_tech = 0;
	points_building = 0;


	# Points for ships
	cursor.execute("SELECT shiplist_ship_id,shiplist_count FROM shiplist WHERE shiplist_user_id='"+user_id+"';")
	res = cursor.fetchall()
	for arr in res:
		p = arr[1] * ships[arr[0]]
		#points += p
		#points_ships = points_ships + p

	#print round(time.time() - t,3)


	# Points for ships in fleets
	cursor.execute("SELECT fs.fs_ship_id, fs.fs_ship_cnt FROM fleet AS f INNER JOIN fleet_ships AS fs ON f.fleet_id = fs.fs_fleet_id AND fs.fs_ship_faked='0'	AND  f.fleet_user_id='"+user_id+"';")
	res = cursor.fetchall()
	for arr in res:
		p = arr[1] * ships[arr[0]]
		#points += p
		#points_ships = points_ships + p

	#print round(time.time() - t,3)


	# Points for defense
	cursor.execute("SELECT deflist_def_id,deflist_count FROM deflist WHERE deflist_user_id='"+user_id+"';")
	res = cursor.fetchall()
	for arr in res:
		p = arr[1] * defense[arr[0]]
		#points += p
		#points_building = points_building + p

	#print round(time.time() - t,3)


	# Points for buildings
	cursor.execute("SELECT buildlist_building_id,buildlist_current_level FROM buildlist WHERE buildlist_user_id='"+user_id+"';")
	res = cursor.fetchall()
	for arr in res:
		if arr[1] > 0:
			p = buildings[str(arr[0])+":"+str(arr[1])]
			#points += p
			#points_building += p

	#print round(time.time() - t,3)


	# Points for buildings
	cursor.execute("SELECT techlist_tech_id,techlist_current_level FROM techlist WHERE techlist_user_id='"+user_id+"';")
	res = cursor.fetchall()
	for arr in res:
		if arr[1] > 0:
			p = techs[str(arr[0])+":"+str(arr[1])]
			#points += p
			#points_tech += p




	#print round(time.time() - t,3),' ',uarr[1]


	# Store calculated points in a list
	udata.append( (user_id,points,points_building,points_tech,points_ships) )

print "Finished", round(time.time() - t,3)

# Save all together to user table
cursor.executemany("UPDATE users SET user_points=%s,user_points_buildings=%s,user_points_tech=%s,user_points_ships=%s WHERE user_id=%s",udata)

# Set last ranks
cursor.execute("UPDATE users SET user_rank_last=user_rank_current;")

# Empty statistics table
cursor.execute("TRUNCATE TABLE user_stats;")

# Load home sectors
sx = {}
sy = {}
cursor.execute("""SELECT
	cell_sx,
	cell_sy,
	planet_user_id
FROM
	space_cells
INNER JOIN
	planets
	ON planet_solsys_id=cell_id
	AND planet_user_main=1
;""")
res = cursor.fetchall()
for arr in res:
	sx[arr[2]] = arr[0]
	sy[arr[2]] = arr[1]

# Calculate ranks and data for stats table
rank = 1
sdata = []	# Statistics data
rdata = [] # Rank data
cursor.close()
cursor = db.cursor(MySQLdb.cursors.DictCursor)
cursor.execute("""SELECT 
	user_id,
	user_rank_current,
	user_highest_rank,
	user_nick,
	user_points,
	user_points_ships,
	user_points_tech,
	user_points_buildings,
	user_rank_last,
	alliance_tag,
	alliance_id,
	race_name,
	user_last_online,
	user_blocked_to,
	user_hmode_to 
FROM 
	users 
LEFT JOIN 
	alliances 
	ON user_alliance_id=alliance_id 
	AND user_alliance_application='' 
INNER JOIN 
	races ON user_race_id=race_id	
WHERE	
	user_show_stats='1'	
ORDER BY 
	user_points DESC, user_registered DESC, user_nick ASC
	;""")
res = cursor.fetchall()
for arr in res:

	# Highest rank
	if arr["user_highest_rank"] > 0 :
		hr = min(arr['user_highest_rank'],rank)
	else:
		hr = arr['user_rank_current']
	rdata.append( (rank,hr,arr["user_id"]) )

	sdata.append( (arr["user_id"],arr["user_points"],arr["user_points_ships"],arr["user_points_tech"],arr["user_points_buildings"],rank,arr["user_rank_last"],arr["user_nick"],arr["alliance_tag"],arr["alliance_id"],arr["race_name"],sx[arr["user_id"]],sy[arr["user_id"]],arr["user_last_online"],arr["user_hmode_to"],arr["user_blocked_to"]) )
	rank = rank + 1
cursor.close()

cursor = db.cursor()

# Save ranks
cursor.executemany("""UPDATE
		users
	SET
		user_rank_current=%s,
		user_highest_rank=%s
	WHERE
		user_id=%s
	;""", rdata)

# Save stats table
cursor.executemany("""INSERT INTO 
	user_stats 
	(
		user_id,
		user_points,
		user_points_ships,
		user_points_tech,
		user_points_buildings,
		user_rank_current,
		user_rank_last,
		user_nick,
		alliance_tag,
		alliance_id,
		race_name,
		cell_sx,
		cell_sy,
		user_inactive,
		user_hmod,
		user_blocked
	)	
	VALUES(
	%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s
	)
	; """, sdata)
						








print round(time.time() - t,3)

