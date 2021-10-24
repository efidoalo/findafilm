<html>
  <head>
    <title>Film Search - Search Results</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
  </head>
  <body id="background">
    <div id="Center_Panel">
    <?php
      /* Request rate limitng no longer applies.
         TMDB API can only be called 40 times every 10 seconds, if this is exceeded it returns
         status error code 25. This 40/10 s limit is IP dependent, but at the moment this code
         is running on the server regardless of user IP addr. In future if demand requires it
         the API could be called from the users machine/browser using JS. */
      $JSONobj_sCriteria = $_POST["FilmObject"];

      $genre_ids = array("Action"          => 28,
                         "Adventure"       => 12,
                         "Animation"       => 16,
                         "Comedy"          => 35,
                         "Crime"           => 80,
                         "Documentary"     => 99,
                         "Drama"           => 18,
                         "Family"          => 10751,
                         "Fantasy"         => 14,
                         "Historical"      => 36,
                         "Horror"          => 27,
                         "Music"           => 10402,
                         "Mystery"         => 9648,
                         "Romance"         => 10749,
                         "Science Fiction" => 878,
                         "Thriller"        => 53,
                         "War"             => 10752,
                         "Western"         => 37);

      $release_date_ge = array("1900s"   => "1900-1-1",
                               "1910s"   => "1910-1-1",
                               "1920s"   => "1920-1-1",
                               "1930s"   => "1930-1-1",
                               "1940s"   => "1940-1-1",
                               "1950s"   => "1950-1-1", 
                               "1960s"   => "1960-1-1",
                               "1970s"   => "1970-1-1",
                               "1980s"   => "1980-1-1",
                               "1990s"   => "1990-1-1",
                               "2000s"   => "2000-1-1",
                               "2010s"   => "2010-1-1");
      $release_date_le = array("1900s"   => "1909-12-31",
                               "1910s"   => "1919-12-31",
                               "1920s"   => "1929-12-31",
                               "1930s"   => "1939-12-31",
                               "1940s"   => "1949-12-31",
                               "1950s"   => "1959-12-31", 
                               "1960s"   => "1969-12-31",
                               "1970s"   => "1979-12-31",
                               "1980s"   => "1989-12-31",
                               "1990s"   => "1999-12-31",
                               "2000s"   => "2009-12-31",
                               "2010s"   => "2019-12-31");

      // Generate array, $genreCriteria, of strings that define all of the specified genres
      $genreExistence = 1;
      if ( $JSONobj_sCriteria[13]==="]" ) {
        $genreExistence = 0;
      }
      $genreCriteria; // array of genres (if genreExistence==1 )
      $pos = 0;
      $index = 13;
      if ($genreExistence===1) {        
        while ( $JSONobj_sCriteria[$index]!=="]" ) {
          if ($JSONobj_sCriteria[$index]===",") {
             $index+=2;
          }
          $currGenre;
          if ($pos>0) {
            unset($currGenre);
          }
          $local_index = 0;
          while ( $JSONobj_sCriteria[++$index]!=="\"" ) { 
            $currGenre[$local_index++] = $JSONobj_sCriteria[$index];
          }
          $genreCriteria[$pos] = $currGenre;
          ++$index;
          ++$pos;  
        }
        for ($i=0; $i<$pos; ++$i) {
          $genreCriteria[$i] = implode("", $genreCriteria[$i] );
        }
        for ($i=0; $i<count($genreCriteria); ++$i) {
          $len = count($genreCriteria);
          for ($j=$i+1; $j<$len; ++$j) {
             if ($genreCriteria[$i]==$genreCriteria[$j]) {
               unset($genreCriteria[$j]);
             }
          }
          $genreCriteria = array_values($genreCriteria);
        } 
      } 

      // Generate array, $actorCriteria, of strings that define all of the specified actors/actresses
      $actorExistence = 1;
      $index+=14;
      if ( $JSONobj_sCriteria[$index]==="]" ) {
        $actorExistence = 0;
      }
      $actorCriteria; // array of genres (if genreExistence==1 )
      $pos = 0;
      if ($actorExistence===1) {        
        while ( $JSONobj_sCriteria[$index]!=="]" ) {
          if ($JSONobj_sCriteria[$index]===",") {
             $index+=2;
          }
          $currActor;
          if ($pos>0) {
            unset($currActor);
          }
          $local_index = 0;
          while ( $JSONobj_sCriteria[++$index]!=="\"" ) { 
            $currActor[$local_index++] = $JSONobj_sCriteria[$index];
          }
          $actorCriteria[$pos] = $currActor;
          ++$index;
          ++$pos;  
        }
        for ($i=0; $i<$pos; ++$i) {
          $actorCriteria[$i] = implode("", $actorCriteria[$i] );
        }
        for ($i=0; $i<count($actorCriteria); ++$i) {
          $len = count($actorCriteria);
          for ($j=$i+1; $j<$len; ++$j) {
             if ($actorCriteria[$i]==$actorCriteria[$j]) {
               unset($actorCriteria[$j]);
             }
          }
          $actorCriteria = array_values($actorCriteria);
        } 
      } 
      

      // Get decade of film release
      $decade;
      $index+=14;
      $local_index=0;
      while ($JSONobj_sCriteria[$index]!=="\"" ) {
        $decade[$local_index] =   $JSONobj_sCriteria[$index];
        ++$index;
        ++$local_index;
      }
      $decade = implode("", $decade);
      
      // get/fill length criteria, greater than and/or less than lengths. 
      $gStr; // greater than string (string representing base 10 number - minutes)
      $gExistence = 0;
      $lStr; // less than string   (string representing base 10 number - minutes)
      $lExistence = 0;
        
      $index+=14;
      $selector=0; // 0 for less than, 1 for greater than
      while ($JSONobj_sCriteria[$index]!=="]") {
        if ($JSONobj_sCriteria[$index]===",") {
          $index+=2;
        }
        $local_index=0;
        while ($JSONobj_sCriteria[++$index]!=="\"") {
          if ( ($local_index===0) && ($JSONobj_sCriteria[$index]==="<") ) {
            if ($JSONobj_sCriteria[$index+1]!=="\"") {
              $lExistence = 1;
            }
            $selector = 0;
          }            
          else if ( ($local_index===0) && ($JSONobj_sCriteria[$index]===">") ) {
            if ($JSONobj_sCriteria[$index+1]!=="\"") {
              $gExistence = 1;
            }
            $selector = 1;
          }    
          if ($selector===0) {
            $lStr[$local_index++] = $JSONobj_sCriteria[$index];
          }
          else if ($selector===1) {
            $gStr[$local_index++] = $JSONobj_sCriteria[$index];
          }
        }
        ++$index;
      }
      if ($gExistence===1) {
        $gStr = implode("", $gStr);
        $gStr = substr($gStr, 1);
      }
      if ($lExistence === 1) {
        $lStr = implode("", $lStr);
        $lStr = substr($lStr, 1);
      }

      // Generate array, $directorCriteria, of strings that define all of the specified directors
      $directorExistence = 1;
      $index+=17;
      if ( $JSONobj_sCriteria[$index]==="]" ) {
        $directorExistence = 0;
      }
      $directorCriteria; // array of directors (if directorExistence==1 )
      $pos = 0;
      if ($directorExistence===1) {        
        while ( $JSONobj_sCriteria[$index]!=="]" ) {
          if ($JSONobj_sCriteria[$index]===",") {
             $index+=2;
          }
          $currDirector;
          if ($pos>0) {
            unset($currDirector);
          }
          $local_index = 0;
          while ( $JSONobj_sCriteria[++$index]!=="\"" ) { 
            $currDirector[$local_index++] = $JSONobj_sCriteria[$index];
          }
          $directorCriteria[$pos] = $currDirector;
          ++$index;
          ++$pos;  
        }
        for ($i=0; $i<$pos; ++$i) {
          $directorCriteria[$i] = implode("", $directorCriteria[$i] );
        }
        for ($i=0; $i<count($directorCriteria); ++$i) {
          $len = count($directorCriteria);
          for ($j=$i+1; $j<$len; ++$j) {
             if ($directorCriteria[$i]==$directorCriteria[$j]) {
               unset($directorCriteria[$j]);
             }
          }
          $directorCriteria = array_values($directorCriteria);
        } 
      } 


      // Generate array, $keywordCriteria, of strings that define all of the specified keywords
      $keywordExistence = 1;
      $index+=16;
      if ( $JSONobj_sCriteria[$index]==="]" ) {
        $keywordExistence = 0;
      }
      $keywordCriteria; // array of directors (if directorExistence==1 )
      $pos = 0;
      if ($keywordExistence===1) {        
        while ( $JSONobj_sCriteria[$index]!=="]" ) {
          if ($JSONobj_sCriteria[$index]===",") {
             $index+=2;
          }
          $currKeyword;
          if ($pos>0) {
            unset($currKeyword);
          }
          $local_index = 0;
          while ( $JSONobj_sCriteria[++$index]!=="\"" ) { 
            $currKeyword[$local_index++] = $JSONobj_sCriteria[$index];
          }
          $keywordCriteria[$pos] = $currKeyword;
          ++$index;
          ++$pos;  
        }
        for ($i=0; $i<$pos; ++$i) {
          $keywordCriteria[$i] = implode("", $keywordCriteria[$i] );
        }
        for ($i=0; $i<count($keywordCriteria); ++$i) {
          $len = count($keywordCriteria);
          for ($j=$i+1; $j<$len; ++$j) {
             if ($keywordCriteria[$i]==$keywordCriteria[$j]) {
               unset($keywordCriteria[$j]);
             }
          }
          $keywordCriteria = array_values($keywordCriteria);
        } 
      } 
      
      // set URL
      $ch = mysqli_init();
      $conn = mysqli_connect("localhost", "id555985_diltoid", "r1d1z", "id555985_person_data");
      mysqli_select_db($conn, "id555985_person_data");
      $sql = 'SELECT person_name, person_id 
        FROM person_ids_tbl
        WHERE person_name="';
      $URL = "https://api.themoviedb.org/3/discover/movie?api_key=5bfb5497459e111db0d320341f304274"
              ."&include_adult=false&page=1"
              .("&primary_release_data.gte=".$release_date_ge[$decade])
              .("&primary_release_date.lte=".$release_date_le[$decade]);

      // append actor/actress information to URL
      if ($actorExistence===1) {
        $tempCount = 0;
        for ($i=0; $i<count($actorCriteria); ++$i) {      
          if (($retval = mysqli_query($conn, $sql.$actorCriteria[$i]."\"" ))!==false) {
            $res_str = (mysqli_fetch_array($retval))[1];
            if (strlen($res_str)>0) {
              if ($tempCount>0) {
                $URL = $URL.",".$res_str;
              }
              else {
                $URL = $URL."&with_cast=".$res_str;           
              }
              ++$tempCount;
            }
          }
        }
      }
      $tempCount=0;

      
      // append director information.
      if ($directorExistence===1) {
        for ($i=0; $i<count($directorCriteria); ++$i) {      
          if (($retval = mysqli_query($conn, $sql.$directorCriteria[$i]."\"" ))!==false) {
            $res_str = (mysqli_fetch_array($retval))[1];
            if (strlen($res_str)>0) {
              if ($tempCount>0) {
                $URL = $URL.",".$res_str;
              }
              else {
                $URL = $URL."&with_crew=".$res_str;           
              }
              ++$tempCount;
            }
          }
        }
      }
      $tempCount=0;
      // append genre information
      if ($genreExistence===1) {
        $URL = $URL."&with_genres=".$genre_ids[$genreCriteria[0]];
        for ($i=1; $i<count($genreCriteria); ++$i) {
          $URL = $URL.",".$genre_ids[$genreCriteria[$i]];
        }
      }
      // append keyword information
      if ($keywordExistence===1) {
        $URL = $URL."&with_keywords=".$keywordCriteria[0];
        for ($i=1; $i<count($keywordCriteria); ++$i) {
          $URL = $URL.",".$keywordCriteria[$i];
        }
      }
      // append film length/runtime information
      if ($gExistence===1) {
        $URL = $URL."&with_runtime.gte=".$gStr;
      }
      if ($lExistence===1) {
        $URL = $URL."&=with_runtime.lte=".$lStr;
      }

      // initiate curl library
      $ch = curl_init();
  
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

 
      curl_setopt($ch, CURLOPT_URL, $URL);
      $results = curl_exec($ch);
      while (substr($results, 0, 17)==="{\"status_code\":25") {
        $results = curl_exec($ch);
      }

      // get an array of movie ids (TMDB ids) that matches the specified film criteria
      $pos=0;
      $IdArray=array();
      for ($i=0; $i<strlen($results); ++$i) {
        if (substr($results, $i, 5)==="\"id\":") {
          $local_index=0;
          $index = 4;
          while ($results[$i + (++$index)]!==",") {
            ++$local_index;
          }
          $IdArray[$pos] = substr($results, $i+5, $local_index);
          ++$pos;
        } 
      }
      if (count($IdArray)===0) {
        echo "No films were found that match your criteria. Please go back and refine the search.";
        exit();
      }
 
    /*  // get configuration data
      curl_setopt($ch, CURLOPT_URL, "https://api.themoviedb.org/3/configuration?api_key=5bfb5497459e111db0d320341f304274");
      $configs = curl_exec($ch);
      while (substr($configs, 0, 17)==="{\"status_code\":25") {
        $configs = curl_exec($ch);
      }
      //printf("\n\n  %s  ", $configs);   */

    ?>
    <?php 
      // print the film information for each movie (search result) id 
      for ($i=0; $i<count($IdArray); ++$i) { ?>
      <div class="Result_box1">
        <div class="Result_box2">
          <div class="Result_box3">
            <div class="Img_Poster">
              <img src="
                <?php
                  unset($imageURL, $imRes,$posterData, $currHeight, $currWidth, $img_path );
                  
                  $imageURL = "https://api.themoviedb.org/3/movie/".$IdArray[$i]."/images?api_key=5bfb5497459e111db0d320341f304274";
                  curl_setopt($ch, CURLOPT_URL, $imageURL);     
                  $imRes = curl_exec($ch);
                  while (substr($imRes, 0, 17)==="{\"status_code\":25") {
                    $imRes = curl_exec($ch);
                  }
                  $base = "https://image.tmdb.org/t/p/w154";  // line 410
                                                // line 410
                  for ($loc=0; $loc<strlen($imRes); ++$loc) {
                    if (substr($imRes, $loc, 10)==="\"posters\":") {
                      $posterData = substr($imRes, $loc+11);
                      // No Image available
                      if ($posterData==="]}") {
                        
                        echo " width=154 height=308 alt=\"movie_poster\"";
                      }
                      $image_found=0;
                      for ($j=0; $j<strlen($posterData); ++$j) {
                        if ($posterData[$j]==="{") {               
                          $image_found=0;                                          
                          for ($k=$j+1; $k<strlen($posterData); ++$k) {
                            if (substr($posterData, $k, 9)==="\"height\":") {
                              $currHeight=array();
                              $index = $k+9;
                              $local_index=0;
                              while ($posterData[$index]!==",") {
                                $currHeight[$local_index++]=$posterData[$index++];
                              }
                              $k=$index;
                              $currHeight = implode("", $currHeight);               
                              $currHeight = floatval($currHeight);
                              ++$image_found;
                            } 
                            if (substr($posterData, $k, 8)==="\"width\":") {
                              $currWidth=array();
                              $index = $k+8;
                              $local_index=0;
                              while ($posterData[$index]!=="}") {
                                $currWidth[$local_index++]=$posterData[$index++];
                              }                                                       
                              $k=$index;
                              $currWidth = implode("", $currWidth); 
                              $currWidth = floatval($currWidth);
                              ++$image_found;
                            }
                            if ($image_found===2) {
                              $k=strlen($posterData);
                              if (abs(($currHeight/$currWidth) - 1.5)<0.01) {
                                ++$image_found;
                              }                                                        
                            } 
                            if ($image_found === 3 ) {
                              for ($k=$j+1; $k<strlen($posterData); ++$k) {
                                if ( substr($posterData, $k, 13)==="\"file_path\":\"" ) {
                                  $index = $k+13;
                                  $local_index = 0;
                                  $img_path=array();
                                  while ($posterData[$index]!=="\"") { // line 418
                                    $img_path[$local_index++] = $posterData[$index++];
                                  }
                                  $img_path = implode("", $img_path);
                                  $base = $base.$img_path."\" width=154 alt=\"movie_poster\"";
                                  echo $base;
                                  $k=strlen($posterData);
                                  $j=$k;
                                  $loc=strlen($imRes);
                                }
                              }
                            }
                          } 
                        }
                      }
                    }
                  }
                ?> 
              /> 
            </div>
            <div class="Movie_Header"> 
              <div class="Movie_title_box">
                <p class="Movie_title">
                  <?php
                    // Get movie title, runtime length, genres, and overview. For the specified film
                    for ($j=0; $j<strlen($results); ++$j) {
                      if (substr($results, $j, 11)==="\"results\":[") {
                        $index = $j+11;
                        for ($k=$j; $k<strlen($results); ++$k) {
                          if ($results[$k]==="{") {
                            for ($p=$k; $p<strlen($results); ++$p) {
                              if (substr($results, $p, 5 )==="\"id\":") {
                                $index = $p+5;
                                $local_index=0; 
                                $currId = array();
                                while ($results[$index]!==",") {
                                  $currId[$local_index++] = $results[$index++]; 
                                }
                                $p=strlen($results);
                                $currId = implode("", $currId);
                                if ($currId===$IdArray[$i])  {
                                  for ($p=$k; $p<strlen($results); ++$p) {
                                    if (substr($results, $p, 9)==="\"title\":\"") {
                                      $index = $p+9;
                                      $local_index=0;
                                      $currTitle = array();
                                      while ( $results[$index] !== "\"") {
                                        $currTitle[$local_index++] = $results[$index++];
                                      }
                                      $currTitle = implode("", $currTitle);
                                      $p=strlen($results);
                                    }
                                  }
                                  for ($p=$k; $p<strlen($results); ++$p) {
                                    if (substr($results, $p, 12)==="\"overview\":\"") {
                                      $index = $p+12;
                                      $local_index=0;
                                      $currOverview = array();
                                      while ( substr($results,$index , 14 )!== "\",\"popularity\"") {
                                        $currOverview[$local_index++] = $results[$index++];
                                      }
                                      $currOverview = implode("", $currOverview);
                                      $p=strlen($results);
                                      $currYear=array();
                                      while ( substr($results,$index , 16 )!== "\",\"release_date\"") {
                                        ++$index;
                                      }
                                      $index+=18;
                                      $local_index=0;
                                      while ($results[$index]!=="-") {
                                        $currYear[$local_index++] = $results[$index++];
                                      }
                                      $currYear = implode("",$currYear);
                                      $p=strlen($results);
                                      echo $currTitle." (".$currYear.") ";
                                    }
                                  }
                                  for ($p=$k; $p<strlen($results); ++$p) {
                                    if (substr($results, $p, 13)==="\"genre_ids\":[") {
                                      $index = $p+12;
                                      $pos=0;
                                      $movieGenres = array();
                                      while ($results[$index]!=="]") {
                                        ++$index;
                                        $movieGenres[$pos] = array();
                                        $local_index = 0;
                                        while ( ($results[$index]!==",") && ($results[$index]!=="]") ) {
                                          ($movieGenres[$pos])[$local_index++] = $results[$index++];
                                        }
                                        $movieGenres[$pos] = implode("", $movieGenres[$pos]);
                                        ++$pos;
                                      } 
                                      $p=strlen($results);
                                      $k=$p;
                                      $j=$k;
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      } 
                    }
                  ?> 
                </p>
              </div>
              <div class="Movie_runtime_box">
                <p class="Movie_runtime">
                  <?php
                    // get film runtime data 
                    $imageURL = "https://api.themoviedb.org/3/movie/"
                        .$IdArray[$i]."?api_key=5bfb5497459e111db0d320341f304274";
                    curl_setopt($ch, CURLOPT_URL, $imageURL);
                    $Res = curl_exec($ch);
                    while (substr($Res, 0, 17)==="{\"status_code\":25") {
                      $Res = curl_exec($ch);
                    }
                    for ($j=0; $j<strlen($Res); ++$j) {
                      if ( substr($Res, $j, 10 )==="\"runtime\":" ) {
                        $index = $j+10;
                        $local_index = 0;
                        $currRuntime = array();
                        while ($Res[$index]!==",") {
                          $currRuntime[$local_index++] = $Res[$index++];
                        }
                        $currRuntime = implode("", $currRuntime);
                        $j=strlen($Res);
                        echo $currRuntime." mins";
                      } 
                    }
                  ?>
                </p>
              </div>
              <div class="Movie_genrelist_box">
                <p class="Movie_genrelist">
                  <?php
                    for ($j=0; $j<min(count($movieGenres), 5); ++$j) {
                      $genreString = array_search ($movieGenres[$j], $genre_ids);
                      if ($genreString === "Science Fiction") {
                        $genreString = "Sci-Fi";
                      }
                      if ($j>0) {
                        echo "<br>";
                      }
                      echo $genreString;
                    }                    
                  ?>
                </p>
              </div>
            </div> 
            <?php 
              if ( ($actorExistence===1) || ($directorExistence===1) ) { 
            ?>
            <div class="Movie_overview_box">
              <p class="Movie_overview">
                <?php
                  echo $currOverview;
                ?>
              </p>
            </div>
            <div class="People_box">
              <?php
                  echo "People:   ";
                  $count =0;
                  $peopleURL = "https://api.themoviedb.org/3/movie/"
                              .$IdArray[$i]."/credits?api_key=5bfb5497459e111db0d320341f304274";
                  curl_setopt($ch, CURLOPT_URL, $peopleURL);
                  $res = curl_exec($ch);
                  while (substr($res, 0, 17)==="{\"status_code\":25") {
                    $res = curl_exec($ch);
                  }
                  for ($j=0; $j<strlen($res); ++$j) {
                    if (substr($res, $j, 8)==="\"name\":\"")  {
                      $index = $j+8;
                      $local_index = 0;
                      $currPerson = array(); 
                      while ($res[$index]!=="\"") {
                        $currPerson[$local_index++] = $res[$index++];  
                      }
                      $currPerson = implode("", $currPerson);
                      if ($actorExistence===1) {
                        for ($k=0; $k<count($actorCriteria); ++$k) {
                          if ($actorCriteria[$k]===$currPerson) {
                            if ($count>0) {
                              echo ", ".$currPerson;
                            }
                            else {
                              echo $currPerson;
                            }
                            $k = count($actorCriteria);
                            ++$count;
                          }
                        }
                      }
                      if ($directorExistence===1) {
                        for ($k=0; $k<count($directorCriteria); ++$k) {
                          if ($directorCriteria[$k]===$currPerson) {
                            if ($count>0) {
                               echo ", ".$currPerson;
                             }
                             else {
                               echo $currPerson;
                             }
                             $k = count($directorCriteria);
                             ++$count;
                          }
                        }
                      }
                    }     
                  }                              
              ?>
            </div>
          <?php } ?>
          <?php 
            if ( ($actorExistence===0) && ($directorExistence===0) )  { 
          ?>
            <div class="Movie_overview_box_no_people">
              <p class="Movie_overview_no_people">
                <?php
                  echo $currOverview;
                ?>
              </p>
            </div>
          <?php }  ?>
          </div>
        </div>
      </div>

    <?php }  ?>
    
    </div>
  </body>
</html>
