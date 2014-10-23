#!/bin/sh

##
## Database parameters
##
dbname=wgs
dbuser=wgs

export PGPASSWORD='wgs'

pgsql_dir=/usr/pgsql-9.2
pgsql_port=5432

## DDL sql path
ddl_sql_dir='../sql'


##
## Sub routine : exec sql in specified directory
##
exec_sql ()
{
    sub_dir=$1
    message=$2
    target_dir="$ddl_sql_dir/${sub_dir}.d"

    if [ -d $target_dir ]; then
        if [ -f "$target_dir/enable" ]; then
            echo
            echo "$message: "
            for i in `ls $target_dir/*.sql`
            do
                echo $i
                $pgsql_dir/bin/psql -U $dbuser -p $pgsql_port -f $i -d $dbname
            done
        fi
    fi
}


##
## Run sql script
##
echo "Drop Database: "
$pgsql_dir/bin/dropdb -U $dbuser -p $pgsql_port $dbname

echo "Create Database: "
$pgsql_dir/bin/createdb -U $dbuser -p $pgsql_port -E UTF-8 $dbname

exec_sql "previous" "Before run ddl"

if [ -f "$ddl_sql_dir/project_ddl.sql" ]; then
    echo "Create Table: "
    $pgsql_dir/bin/psql -U $dbuser -p $pgsql_port -f $ddl_sql_dir/project_ddl.sql -d $dbname
else
    echo "DDL Not found."
    exit;
fi

exec_sql "constraint" "Alter Constraint"
exec_sql "index" "Create Index"
exec_sql "function" "Create Function"
exec_sql "trigger" "Create Trigger"
exec_sql "sequence" "Create Sequence"
exec_sql "grant" "Grant Privliedge"
exec_sql "view" "Create View"
exec_sql "mdata" "Set Master Data"
exec_sql "test" "Set Test Data"
