<?
#================================
# Turbine dataset_query library: Create Turbine DataSet from database queries.
# Included functions:
#   loadDataSetMySQL - create a Turbine DataSet from mySQL query result.
#   loadDataSetSQLite - create a Turbine DataSet from an SQLite query result.
#   loadDataSetODBC - create a Turbine DataSet from an ODBC query result.
#   


#================================
# loadDataSetMySQL - create a Turbine DataSet from mySQL query result
#   $turbine - the turbine object
#   $recordSet - the results of a succesful mySQL query
#   $dataSet - the DataSet name to create

function loadDataSetMySQL($turbine, $recordSet, $dataSet){
#get the column fields
  $num_fields = mysql_num_fields( $recordSet );

  $fields = "";
  for($i=0; $i<$num_fields; $i++){
    if($i>0)
      $fields .= ",";

    $fields .= mysql_field_name( $recordSet, $i);
  }

  $turbine->MakeDataSet($dataSet, $fields);

# get the values
  while ( $row = mysql_fetch_row($recordSet) ) {
    $values="";
    for($i=0; $i<$num_fields; $i++){
      if($i>0)
        $values .= ",";

      $values .= "\x1f" . $row[$i] . "\x1f"; # "\x1f" is a safe delimiter for column values
    }

    $turbine->AddDataSetRow($values);
  }

}


#================================
# loadDataSetSQLite - create a Turbine DataSet from an SQLite query result
#   $turbine - the turbine object
#   $recordSet - the results of a succesful SQLite query
#   $dataSet - the DataSet name to create

function loadDataSetSQLite($turbine, $recordSet, $dataSet){
#get the column fields
  $num_fields = sqlite_num_fields( $recordSet );

  $fields = "";
  for($i=0; $i<$num_fields; $i++){
    if($i>0)
      $fields .= ",";

    $fields .= sqlite_field_name( $recordSet, $i);
  }

  $turbine->MakeDataSet($dataSet, $fields);

# get the values
  while ( $row = sqlite_fetch_array($recordSet) ) {
    $values="";
    for($i=0; $i<$num_fields; $i++){
      if($i>0)
        $values .= ",";

      $values .= "\x1f" . $row[$i] . "\x1f"; # "\x1f" is a safe delimiter for column values
    }

    $turbine->AddDataSetRow($values);
  }

}


#================================
# loadDataSetODBC - create a Turbine DataSet from an ODBC query result
#   $turbine - the turbine object
#   $recordSet - the results of a succesful ODBC query
#   $dataSet - the DataSet name to create

function loadDataSetODBC($turbine, $recordSet, $dataSet){
#get the column fields
  $num_fields = odbc_num_fields( $recordSet );

  $fields = "";
  for($i=1; $i <= $num_fields; $i++){
    if($i>1)
      $fields .= ",";

    $fields .= odbc_field_name( $recordSet, $i);
  }

  $turbine->MakeDataSet($dataSet, $fields);

# get the values
  while ( odbc_fetch_row($recordSet) ) {
    $values="";

    for($i=1; $i <= $num_fields; $i++){
      if($i>1)
        $values .= ",";

      $value = odbc_result ($recordSet, $i);
      $values .= "\x1f" . $value . "\x1f"; # "\x1f" is a safe delimiter for column values
    }

    $turbine->AddDataSetRow($values);
  }

}


?>