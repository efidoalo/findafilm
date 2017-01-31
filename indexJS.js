/*====================================;
 *
 * File: indexJS.js 
 * Content: Functions for enhanced index.html
 * page functionality.
 * Date: 12/1/2017
 *
 **************************************/

/* Function to add a genre when the Add Genre button is 
  pressed */
function AddGenre()
{ 
  var Genre_DD_elem = document.getElementById("Genre_DD");
  var genreString = Genre_DD_elem.options[Genre_DD_elem.selectedIndex].text;
 
  var para = document.createElement("p");
  var textnode = document.createTextNode(genreString);
  para.appendChild(textnode);

  var att = document.createAttribute("id");
  if (genreString==="Science Fiction") 
    genreString="Sci-Fi"; 
  att.nodeValue = genreString; 
 
  para.setAttributeNode(att);
  
  var GenreContainers = document.getElementById("Selected_Genres").children;
  var NoOfChilds = document.getElementById("Selected_Genres").childElementCount;
  
  for (var i=0; i<NoOfChilds; ++i) {
    if (GenreContainers[i].childElementCount===0) {
      GenreContainers[i].appendChild(para);
      i=NoOfChilds;
    }
  }
}

// Deletes all of the currently added genres.
function resetGenres() 
{
  var GenreContainers = document.getElementById("Selected_Genres").children;
  var NoOfChilds = document.getElementById("Selected_Genres").childElementCount;
  for (var i=0; i<NoOfChilds; ++i) {
    if (GenreContainers[i].childElementCount>0)
      GenreContainers[i].removeChild(GenreContainers[i].children[0]);
  } 
}

function addActor()
{
  var ActorInput = document.getElementById("Actor_Input");
  var currActor = ActorInput.value;
  var ActorContainers = document.getElementById("Selected_Actors");

  var para = document.createElement("p");

  var text = document.createTextNode(currActor);
  para.appendChild(text);

  var att = document.createAttribute("class");
  att.nodeValue = "Actor";

  para.setAttributeNode(att);

  if (currActor.length>0) {  
    for (var i=0; i<ActorContainers.childElementCount; ++i) {
      if ( ActorContainers.children[i].childElementCount===0 ) {
        ActorContainers.children[i].appendChild(para);
        i=ActorContainers.childElementCount;
      }
    }
  }
}

function resetActor()
{
  var ActorContainers = document.getElementById("Selected_Actors").children;
  for (var i=0; i<document.getElementById("Selected_Actors").childElementCount; ++i) {
    if (ActorContainers[i].childElementCount>0) 
     ActorContainers[i].removeChild(ActorContainers[i].children[0]); 
  }
}

// Display input boxes dependent upon Length drop down menu 
function LengthDisplay()
{
  var lengthSelector = document.getElementById("Length_Selector");
  var selectorType = lengthSelector.options[lengthSelector.selectedIndex].text;
  var str1 = "Less than", str2="Greater than", str3 = "Between";
  var infoStore = document.getElementById("Length_Selector2");
 

  if ( (selectorType===str1) || (selectorType===str2))  {
    infoStore.children[2].style.display="none";
    infoStore.children[3].style.display="none";
    infoStore.children[4].style.display="none";
    infoStore.children[1].style.display="block";
    infoStore.children[0].style.display="block";
  }
  else if ( selectorType === str3 ) {
    infoStore.children[2].style.display="block";
    infoStore.children[3].style.display="block";
    infoStore.children[1].style.display="block";
    infoStore.children[4].style.display="block"; 
    infoStore.children[0].style.display="block";
  }
}

function addDirector() {
  var DirectorInput = document.getElementById("Director_Input");
  var currDirector = DirectorInput.value;
  var DirectorContainers = document.getElementById("Selected_Directors");

  var para = document.createElement("p");

  var text = document.createTextNode(currDirector);
  para.appendChild(text);

  var att = document.createAttribute("class");
  att.nodeValue = "Director";

  para.setAttributeNode(att);

  if (currDirector.length>0) {
    for (var i=0; i<DirectorContainers.childElementCount; ++i) {
      if ( DirectorContainers.children[i].childElementCount===0 ) {
        DirectorContainers.children[i].appendChild(para);
        i=DirectorContainers.childElementCount;
      }
    }
  }

}

function resetDirector() {
  var DirectorContainers = document.getElementById("Selected_Directors").children;
  for (var i=0; i<document.getElementById("Selected_Directors").childElementCount; ++i) {
    if (DirectorContainers[i].childElementCount>0)
     DirectorContainers[i].removeChild(DirectorContainers[i].children[0]);
  }

}

function addKeyword() 
{
  var KeywordInput = document.getElementById("Keyword_Input");
  var currKeyword = KeywordInput.value;
  var KeywordContainers = document.getElementById("Selected_Keywords");

  var para = document.createElement("p");

  var text = document.createTextNode(currKeyword);
  para.appendChild(text);

  var att = document.createAttribute("class");
  att.nodeValue = "Keyword";

  para.setAttributeNode(att);

  if (currKeyword.length>0) {
    for (var i=0; i<KeywordContainers.childElementCount; ++i) {
      if ( KeywordContainers.children[i].childElementCount===0 ) {
        KeywordContainers.children[i].appendChild(para);
        i=KeywordContainers.childElementCount;
      }
    }
  }

}

function resetKeyword() 
{
  var KeywordContainers = document.getElementById("Selected_Keywords").children;
  for (var i=0; i<document.getElementById("Selected_Keywords").childElementCount; ++i) {
    if (KeywordContainers[i].childElementCount>0)
     KeywordContainers[i].removeChild(KeywordContainers[i].children[0]);
  }
}



// On mousehover over search button, the hidden text form is filled
// with the string value of a JSON object that defines the search 
// options for the current page
function createJSONobject()
{
  var count=0, i=0;
  var Genres = document.getElementById("Selected_Genres").children;
  var Actors = document.getElementById("Selected_Actors").children;
  var Decade_Selector = document.getElementById("Decade_Selector"),
      Decade = Decade_Selector.options[Decade_Selector.selectedIndex].text;
  var Length_Selector = document.getElementById("Length_Selector"),
      Length_type = Length_Selector.options[Length_Selector.selectedIndex].text;
  var mins1 = document.getElementById("LessthanInput").value,
      mins2;
  if (Length_type==="Between" )
    mins2 = document.getElementById("LessthanInput1").value;
  var Directors = document.getElementById("Selected_Directors").children;
  var Keywords = document.getElementById("Selected_Keywords").children;
  
  // JSON object passed as form that determines the film criteria to be search for=.
  var filmCriteria = "{";
  
  filmCriteria+= " \"genres\": [";  
  for (i=0; i<document.getElementById("Selected_Genres").childElementCount; ++i) {
    if (Genres[i].childElementCount>0) {
      if (count>0) 
        filmCriteria+=", ";
      filmCriteria+="\"";
      filmCriteria+=Genres[i].children[0].innerHTML;
      filmCriteria+="\"";
      ++count;
    }
  }
  count=0;  
  filmCriteria+="], ";
  
  filmCriteria+="\"Actors\": [";
  for (i=0; i<document.getElementById("Selected_Actors").childElementCount; ++i) {
    if (Actors[i].childElementCount>0) {
      if (count>0)
        filmCriteria+=", ";
      filmCriteria+="\"";
      filmCriteria+=Actors[i].children[0].innerHTML;
      filmCriteria+="\"";
      ++count;
    }
  }  
  count=0;
  filmCriteria+="], ";
  
  filmCriteria+="\"Decade\": \"";
  filmCriteria+=Decade;
  filmCriteria+="\", ";
  
  filmCriteria+="\"Length\": [";  
  if (Length_type==="Less than") {
    filmCriteria+="\"<";
    filmCriteria+=mins1.toString();
    filmCriteria+="\"";
  } 
  if (Length_type==="Greater than") {
    filmCriteria+="\">";
    filmCriteria+=mins1.toString();
    filmCriteria+="\"";
  }
  if (Length_type==="Between") {
    var minVal = Math.min(mins1,mins2);
    var maxVal = Math.max(mins1,mins2);
    filmCriteria+="\">"; 
    filmCriteria+=minVal.toString();
    filmCriteria+="\", \"<";
    filmCriteria+=maxVal.toString();
    filmCriteria+="\"";
  }
  filmCriteria+="], ";
  
  filmCriteria+="\"Directors\": [";
  for (i=0; i<document.getElementById("Selected_Directors").childElementCount; ++i) {
    if (Directors[i].childElementCount>0) {
      if (count>0)
        filmCriteria+=", ";
      filmCriteria+="\"";
      filmCriteria+=Directors[i].children[0].innerHTML;
      filmCriteria+="\"";
      ++count;
    }
  } 
  count=0;
  filmCriteria+="], ";
 
  filmCriteria+="\"Keywords\": [";
  for (i=0; i<document.getElementById("Selected_Keywords").childElementCount; ++i) {
    if (Keywords[i].childElementCount>0) {
      if (count>0)
        filmCriteria+=", ";
      filmCriteria+="\"";
      filmCriteria+=Keywords[i].children[0].innerHTML;
      filmCriteria+="\"";
      ++count;
    }
  } 
  count=0;
  filmCriteria+="] }";
  
  document.getElementById("Film_Criteria_Object").value = filmCriteria;

} 

// Add event listeners to relevant elements on page load.
// Also hide 2nd length input box to match the initial length drop down menu
// set to the option "Less than".
window.onload = function() {
  var length_boxs = document.getElementById("Length_Selector2");
  length_boxs.children[2].style.display = "none";
  length_boxs.children[3].style.display = "none";
  length_boxs.children[4].style.display = "none";

  document.getElementById("Add_Genre").addEventListener("click", function() {AddGenre();});
  document.getElementById("Reset_Genres").addEventListener("click", function() {resetGenres();});
  document.getElementById("Add_Actor").addEventListener("click", function() {addActor();});
  document.getElementById("Reset_Actor").addEventListener("click", function() {resetActor();});
  document.getElementById("Length_Selector").addEventListener("change", function() {LengthDisplay();});
  document.getElementById("Add_Director").addEventListener("click", function() {addDirector();});
  document.getElementById("Reset_Director").addEventListener("click", function() {resetDirector();});                     document.getElementById("Add_Keyword").addEventListener("click", function() {addKeyword();});
  document.getElementById("Reset_Keyword").addEventListener("click", function() {resetKeyword();});
  document.getElementById("Search_Button").addEventListener("mouseover", function() { createJSONobject();});
}