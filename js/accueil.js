function popupPost() {
  document.getElementById("popupPost").style.display = "block";
}

function closePopup() {
  document.getElementById("popupPost").style.display = "none";
}

function showProfil() {
  let liste = document.getElementById("menuDeroulant").style.display;

  if (liste === "grid") {
    document.getElementById("menuDeroulant").style.display = "none";
  } else {
    document.getElementById("menuDeroulant").style.display = "grid";
  }
}

function clickPost(post) {
  document.location.href = `post.php?id=${post.id}`;
} 

function desactivateRecherche() {
  document.getElementById("containerProfil").classList.remove("displayNone");
  document.getElementById("ecrireTweetTelephone").classList.remove("displayNone");
  document.getElementById("contenaireRechercher").style.display = null;
  document.getElementById("contenaireRechercher").classList.remove("affichageTelephone");
}

function activateRechercher() { 
  document.getElementById("containerProfil").classList.add("displayNone");
  document.getElementById("ecrireTweetTelephone").classList.add("displayNone");
  document.getElementById("contenaireRechercher").style.display = "grid";
  //oblige de mettre le style car la classe est overwrite par l'id 
  document.getElementById("contenaireRechercher").classList.add("affichageTelephone");
  window.addEventListener('click', function checkIn(e){   
    if (!document.getElementById('contenaireRechercher').contains(e.target) && !document.getElementById('rechercherImage').contains(e.target)){
      desactivateRecherche()
      window.removeEventListener('click', checkIn)
    }
  });
  document.getElementById("inputRechercher").focus();

}

function desactivateRechercheAccueil() {
  document.getElementById("containerProfil").classList.remove("displayNone");
  document.getElementById("contenaireRechercher").style.display = null;
  document.getElementById("contenaireRechercher").classList.remove("affichageTelephone");
}


function activateRechercherAccueil() {
  document.getElementById("containerProfil").classList.add("displayNone");
  document.getElementById("contenaireRechercher").style.display = "grid"; 
  //oblige de mettre le style car la classe est overwrite par l'id 
  document.getElementById("contenaireRechercher").classList.add("affichageTelephone");
  window.addEventListener('click', function checkIn(e){   
    if (!document.getElementById('contenaireRechercher').contains(e.target) && !document.getElementById('rechercherImage').contains(e.target)){
      desactivateRechercheAccueil()
      window.removeEventListener('click', checkIn)
    }
  });
  document.getElementById("inputRechercher").focus();
}

function showHint(str, vue) {
  if (str.length == 0) {
    if (vue == "telephone") {
      document.getElementById("listUsers").innerHTML = "";
    }
    else {
      document.getElementById("listUsers").innerHTML = "";
    }
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        let reponses = this.responseText.split("+");
        let liste = document.getElementById("listUsers");
        deleteListe();
        reponses.forEach((reponse) => {
          if (reponse != "") {
            let newLi = document.createElement("li");
            let newA = document.createElement("a");
            newA.textContent = reponse;
            newA.setAttribute("href", `account.php?id=${reponse}`);
            newA.setAttribute("id", reponse);
            newA.setAttribute("class", "aUser");
            newLi.setAttribute("class", "liUser");
            newLi.appendChild(newA);
            liste.appendChild(newLi);
          }
        });
      }
    };
    xmlhttp.open("GET", "ajax.php?q=" + str, true);
    xmlhttp.send();
  }
}

function deleteListe() {
  let list = document.getElementById("listUsers");
  list.innerHTML = "";
  
}

function back() {
  history.back();
}

function backToAccueil() {
  document.location.href = "accueil.php?posts=true";
}
