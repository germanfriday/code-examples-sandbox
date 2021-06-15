function checkInput(form) {
   var zipString = String(form.zip.value) ;
   var cityString = String(form.city.value) ;
   var stateString = String(form.state.value) ;
   document.getElementById("zipError").innerHTML = "" ;
   document.getElementById("stateError").innerHTML = "" ;
   
   // See if we have any input at all
   if (zipString.length == 0 && cityString.length == 0 && stateString.length == 0) {
      document.getElementById("stateError").innerHTML = "ZIP Code or City/State must be entered" ;
      return false ;
   }
   
   // Validate that the ZIP code is valid if it is entered
   if (!(zipString.length == 0 || zipString.length == 5)) {
      document.getElementById("zipError").innerHTML = "Invalid ZIP Code" ;
      return false ;
   } else if (zipString.length == 5) {
      if (isNumeric(zipString)) {
         // We have a valid zip code so continue
         return true ;
      } else {
         document.getElementById("zipError").innerHTML = "Invalid ZIP Code" ;
         return false ;
      }
   } 
   
   // Check that they entered a city
   if (cityString.length == 0) {
      document.getElementById("stateError").innerHTML = "A city must be entered" ;
      return false ;
   }
   
   // Check that a state has been choosen if they enter a city
   if (cityString.length != 0 && stateString.length == 0) {
      document.getElementById("stateError").innerHTML = "State must be selected" ;
      return false ;
   }
   
   // Passed all tests so input must be valid
   return true ;
}


function isNumeric(text) {
   var validChars = "0123456789" ;   
   for (i = 0; i < text.length; i++) { 
      if (validChars.indexOf(text.charAt(i)) == -1) {
         return false ;
         }
      }
   return true ;
}
