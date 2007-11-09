
//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// Programmiert von Nicolas Perrenoud				 		//
// www.nicu.ch | mail@nicu.ch								 		//
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////
//
// 	Dateiname: etoa_stats.c
// 	Topic: C Statistik-Berechnung
// 	Autor: Nicolas Perrenoud alias MrCage
// 	Erstellt: 17.5.2007
// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
// 	Bearbeitet am: 17.05.2007
// 	Kommentar:
//

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <mysql/mysql.h>
#include <stdbool.h>
#include <time.h>
#include <sys/times.h>

// Database constants
char* HOST;
char* USER;
char* PASSWD;
char* DB_NAME;

// Defining some structs
struct game_obj	{
	int id;
	int level;
	float points;
};	
	
/**
* Opens the db connection
*/ 
bool db_connect(MYSQL** mysql)
{
  *mysql = mysql_init(NULL);	
	if (!mysql_real_connect(*mysql, HOST, USER, PASSWD, DB_NAME, MYSQL_PORT, NULL, 0))
	{
    printf("Failed to connect to database: Error: %s\n", mysql_error(*mysql));	
    return false;		
	}
	return true;
}

/**
* Closes the db connection
*/
void db_close(MYSQL** mysql)
{
	mysql_close(*mysql);	
}

/**
* Performs a query
*/
MYSQL_RES* db_query(MYSQL** mysql, char* sqlquery)
{
  if (mysql_query(*mysql, sqlquery)!=0)
  {
    printf("Query failed: %s\n", mysql_error(*mysql));	
    exit(-1);		 	
  }
  return mysql_store_result(*mysql);
}


/**
* The Main function - where it all begins
*/
int main(int argc, char* argv[])
{
	// Time measurement
  struct tms tmsbuf1, tmsbuf2;
  clock_t t1, t2;
  double elapsed_time;
  t1 = times(&tmsbuf1);

	
	// Read config
	FILE* stream;
	char line[100];
	
	stream = fopen("../conf.inc.php","r");
	if (stream==NULL)
	{
		printf("Kann Config-Datei nicht öffnen");
		exit;	
	}
	
	size_t *t = malloc(0);
	int nRet;
	char **puffer = (char **)malloc(sizeof(char*));
	int cnt=1;
	char* n;
  while((nRet=getline(puffer, t, stream)) > 0)
  {
     if ((n=strstr(*puffer,"$db_access"))!=0)
     {
	    	n = strtok(*puffer,"'"); n = strtok(NULL,"'");
	    	if (strcmp(n,"server")==0)
	    	{
		    	n = strtok(NULL,"'");	n = strtok(NULL,"'");
	    		HOST=malloc(strlen(n)*sizeof(char));
	    		sprintf(HOST,"%s",n);
	    	}	
	    	if (strcmp(n,"db")==0)
	    	{
		    	n = strtok(NULL,"'");	n = strtok(NULL,"'");
	    		DB_NAME=malloc(strlen(n)*sizeof(char));
	    		sprintf(DB_NAME,"%s",n);
	    	}	
	    	if (strcmp(n,"user")==0)
	    	{
		    	n = strtok(NULL,"'");	n = strtok(NULL,"'");
	    		USER=malloc(strlen(n)*sizeof(char));
	    		sprintf(USER,"%s",n);
	    	}	
	    	if (strcmp(n,"pw")==0)
	    	{
		    	n = strtok(NULL,"'");	n = strtok(NULL,"'");
	    		PASSWD=malloc(strlen(n)*sizeof(char));
	    		sprintf(PASSWD,"%s",n);
	    	}	
     }
     cnt++;

	}
	fclose( stream ); 	
	
	
	
	
	
	// Define mysql variables
  MYSQL *mysql = NULL; // Connection handler
  MYSQL_RES *result;
  MYSQL_RES *result2;
  MYSQL_ROW row;
  MYSQL_ROW row2;
  
  // Define auxiliary variables
  int i=0;
		
	// Connect to Database
	if (db_connect(&mysql))	
	{
		int temp=0;

		// Load ships
		struct game_obj* s;
		long num_ships;
		result = db_query(&mysql,"select ship_id,ship_battlepoints from ships");
		num_ships = mysql_num_rows(result);
		s = (struct game_obj*)malloc(num_ships*sizeof(struct game_obj));
		i=0;
	  while((row = mysql_fetch_row(result)))
	  {
	  	s[i].id = atoi(row[0]);
	  	s[i].points = atof(row[1]);
			i++;
		}
		mysql_free_result(result);

		// Load defense
		struct game_obj* d;
		long num_defense;
		result = db_query(&mysql,"select def_id,def_battlepoints from defense");
		num_defense = mysql_num_rows(result);
		d = (struct game_obj*)malloc(num_defense*sizeof(struct game_obj));
		i=0;
	  while((row = mysql_fetch_row(result)))
	  {
	  	d[i].id = atoi(row[0]);
	  	d[i].points = atof(row[1]);
			i++;
		}		
		mysql_free_result(result);
		
		// Load buildings
		struct game_obj* b;
		long num_buildings;
		result = db_query(&mysql,"SELECT bp_building_id,bp_points,bp_level FROM building_points");
		num_buildings = mysql_num_rows(result);
		b = (struct game_obj*)malloc(num_buildings*sizeof(struct game_obj));
		i=0;
	  while((row = mysql_fetch_row(result)))
	  {
	  	b[i].id = atoi(row[0]);
	  	b[i].points = atof(row[1]);
	  	b[i].level = atoi(row[2]);
			i++;
		}		
		mysql_free_result(result);
		
		// Load tech
		struct game_obj* t;
		long num_techs;
		result = db_query(&mysql,"SELECT bp_tech_id,bp_points,bp_level FROM tech_points");
		num_techs = mysql_num_rows(result);
		t = (struct game_obj*)malloc(num_techs*sizeof(struct game_obj));
		i=0;
	  while((row = mysql_fetch_row(result)))
	  {
	  	t[i].id = atoi(row[0]);
	  	t[i].points = atof(row[1]);
	  	t[i].level = atoi(row[2]);
			i++;
		}		
		mysql_free_result(result);
		

		// Load user
		long points=0;
		long ship_points=0;
		long def_points=0;
		long building_points=0;
		long tech_points=0;
		
		int tp;
		int t_id;
		int t_level;
		int t_count;
		
		char* uid;
		char* sql;
		char* sql_query;

		long num_users;
		result = db_query(&mysql,"SELECT user_id FROM users");
	  while((row = mysql_fetch_row(result)))
	  {
			uid = row[0];

			points = 0;
			ship_points=0;
			def_points=0;
			building_points=0;
			tech_points=0;
			
			// Calculate ship points
    	sql_query  = malloc(86*sizeof(char));
    	sprintf(sql_query,"SELECT shiplist_ship_id,shiplist_count FROM shiplist WHERE shiplist_user_id=%s",uid);
    	
			result2 = db_query(&mysql,sql_query);			
		  while(row2 = mysql_fetch_row(result2))
		  {
		  	t_id = atoi(row2[0]);
		  	t_count = atoi(row2[1]);		  	
		  	for (i=0;i<num_ships;i++)
		  	{
		  		if (s[i].id==t_id)
		  		{
		  			tp = t_count * s[i].points;
						points += tp;
						ship_points += tp;
		  			break;
		  		}
		  	}
			}
			free(sql_query);
			mysql_free_result(result2);
			
    	sql_query  = malloc(167*sizeof(char));
			sprintf(sql_query,"SELECT fs.fs_ship_id,fs.fs_ship_cnt FROM fleet AS f INNER JOIN fleet_ships AS fs ON f.fleet_id = fs.fs_fleet_id	AND f.fleet_user_id=%s AND fs.fs_ship_faked=0",uid);

			result2 = db_query(&mysql,sql_query);			
		  while(row2 = mysql_fetch_row(result2))
		  {
		  	t_id = atoi(row2[0]);
		  	t_count = atoi(row2[1]);		  	
		  	for (i=0;i<num_ships;i++)
		  	{
		  		if (s[i].id==t_id)
		  		{
		  			tp = t_count * s[i].points;
						points += tp;
						ship_points += tp;
		  			break;
		  		}
		  	}
			}
			free(sql_query);			
			mysql_free_result(result2);

			// Calculate defense points
    	sql_query  = malloc(81*sizeof(char));
    	sprintf(sql_query,"SELECT deflist_def_id,deflist_count FROM deflist WHERE deflist_user_id=%s",uid);
    	
			result2 = db_query(&mysql,sql_query);			
		  while(row2 = mysql_fetch_row(result2))
		  {
		  	t_id = atoi(row2[0]);
		  	t_count = atoi(row2[1]);
		  	for (i=0;i<num_defense;i++)
		  	{
		  		if (d[i].id==t_id)
		  		{
		  			tp = t_count * d[i].points;
						points += tp;
						building_points += tp;
		  			break;
		  		}
		  	}
			}
			free(sql_query);	
			mysql_free_result(result2);
			
			// Calculate building points
    	sql_query  = malloc(102*sizeof(char));
    	sprintf(sql_query,"SELECT buildlist_building_id,buildlist_current_level FROM buildlist WHERE buildlist_user_id=%s",uid);
    	
			result2 = db_query(&mysql,sql_query);			
		  while(row2 = mysql_fetch_row(result2))
		  {
		  	t_id = atoi(row2[0]);
		  	t_level = atoi(row2[1]);
		  	for (i=0;i<num_buildings;i++)
		  	{
		  		if (b[i].id==t_id && b[i].level==t_level)
		  		{
		  			tp = b[i].points;
						points += tp;
						building_points += tp;
		  			break;
		  		}
		  	}
			}
			free(sql_query);		
			mysql_free_result(result2);
	
			// Calculate technology points
    	sql_query  = malloc(94*sizeof(char));
    	sprintf(sql_query,"SELECT techlist_tech_id,techlist_current_level FROM techlist WHERE techlist_user_id=%s",uid);
    	
			result2 = db_query(&mysql,sql_query);			
		  while(row2 = mysql_fetch_row(result2))
		  {
		  	t_id = atoi(row2[0]);
		  	t_level = atoi(row2[1]);
		  	for (i=0;i<num_techs;i++)
		  	{
		  		if (t[i].id==t_id && t[i].level==t_level)
		  		{
		  			tp = t[i].points;
						points += tp;
						tech_points += tp;
		  			break;
		  		}
		  	}
			}
			free(sql_query);			
			mysql_free_result(result2);

			// Save result
    	sql_query = malloc(154*sizeof(char));
			sprintf(sql_query,"UPDATE users SET user_points=%i,user_points_ships=%i,user_points_tech=%d,user_points_buildings=%d WHERE user_id=%s",points,ship_points,tech_points,building_points,uid);
			db_query(&mysql,sql_query);
			free(sql_query);			
			
			// Add user_points
    	sql_query = malloc(207*sizeof(char));
			sprintf(sql_query,"INSERT INTO user_points (point_user_id,point_timestamp,point_points,point_ship_points,point_tech_points,point_building_points) VALUES (%s,UNIX_TIMESTAMP(),%i,%i,%i,%i)",uid,points,ship_points,tech_points,building_points);
			db_query(&mysql,sql_query);
			free(sql_query);			
		}
		mysql_free_result(result);
		free(s);
		free(d);
		free(b);
		free(t);


		// Calculate ranks
		int rank=1;
		int user_id;
		int user_rank_current;
		int user_highest_rank;
		int hr;

		db_query(&mysql,"UPDATE users SET user_rank_last=user_rank_current;");
		
		result = db_query(&mysql,"SELECT user_id,user_rank_current,user_highest_rank FROM users WHERE	user_show_stats='1'	ORDER BY user_points DESC,user_registered DESC,user_nick ASC");			
		while(row = mysql_fetch_row(result))
		{
			user_id=atoi(row[0]);
			user_rank_current=atoi(row[1]);
			user_highest_rank=atoi(row[2]);

			if (user_highest_rank>0)
			{                           
				if (user_highest_rank<=rank)
				{
					hr = user_highest_rank;
				}
				else
				{	
					hr = rank;
				}
			}
			else
			{
				hr = user_rank_current;
			}
	    sql_query = malloc(90*sizeof(char));
			sprintf(sql_query,"UPDATE users SET	user_rank_current=%i,user_highest_rank=%i	WHERE user_id=%i",rank,hr,user_id);
			db_query(&mysql,sql_query);
			free(sql_query);						
			rank++;
		}
		mysql_free_result(result);

		// Update config
		db_query(&mysql,"UPDATE config SET config_value=UNIX_TIMESTAMP() WHERE config_name='statsupdate'");

  	db_close(&mysql);
  	
	}	
	
  t2 = times(&tmsbuf2);
  elapsed_time = ((double) (t2-t1)) /1000;
  printf("Stats generated in %2.3f seconds. \n", elapsed_time);
  return 0;
}
