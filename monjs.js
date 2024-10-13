var selectedChat = null;
var pseudo = null;
const wsAdress = "./requestHandler.php";

//Listeners

document.addEventListener("DOMContentLoaded", function(){ 
	if(localStorage.getItem("token")){
		pseudo = decodeTokenData(localStorage.getItem("token")).pseudo;
		switchDisplay();
	}
	
	//Enter key
	document.getElementsByName("message")[0].addEventListener("keypress", function(event) {
	  if (event.key === "Enter") {
		event.preventDefault();
		sendMsg();
	  }
	}); 
});



//Functions

function switchDisplay(){
	document.body.classList.add("bodychat");
	document.getElementById("connectStatus").style.display = "block";
	document.getElementById("pseudoDiv").style.display = "none";
	
	document.getElementById("messageDiv").style.display = "block";
	//document.getElementById("usersInSalonDiv").style.display = "block";
	document.getElementById("chatDiv").style.display = "flex";
	document.getElementById("connectStatus").innerHTML = "Connecté en temps que : " + pseudo + "<button id='decoButton' onclick='deconnect()'>Se déconnecter</button>" +"<button id='decoButton' onclick='inviteUserToSalon()'>Inviter</button>";
	document.getElementById("chat").value="Selectionnez ou creer un salon à gauche ";
	getSalonList();
}

function deconnect(){
	localStorage.clear();
	window.location.reload(true);
}

function register(){
	//A refaire, mais flemme
	let tempPseudo = window.prompt("Entrez votre login");
	let mdp = window.prompt("Entrez votre mdp");
	
	$.ajax({
		url : wsAdress,
		type : "POST",
		data : "pseudo="+tempPseudo+"&mdp="+mdp+"&operation=register",
		success : function(msg, statut){
			alert(JSON.parse(msg).data.msg);
		},
		error : function(msg, statut, err){
			console.log(msg);
		},
	});
	
}

function getMajMsg(){
	if(selectedChat){
		callWithToken("GET","majMsg","selectedChat="+selectedChat,
			function(msg,statut){
				let monChat = msg;
				let monTA = document.getElementById("chat");
				let contenuChat = "";
				monChat.forEach( function(msg){
					contenuChat += "[" + msg.date + "] " + msg.pseudo + " : " + msg.message + "\n"; 
				})
				monTA.value = contenuChat;
			});
	}
}

function addSalon(){
	let res = window.prompt("Entrez le nom du salon");
	if(res && res != ""){
		subscribeToSalon(res);
	}
}

function getSalonList(){
	if(pseudo){
		callWithToken("GET","listSalon","pseudo="+pseudo,
		function(msg, statut){
			console.log(msg, pseudo);
			let data = msg;
			let salonList = document.getElementById("salonList");
			salonList.innerHTML = "";
			data.forEach(function(elem){
				salonList.innerHTML += `
					<button data-salon='`+elem+`' name='salonButton' onclick="switchToSalon('`+elem+`',this)">`+elem.charAt(0).toUpperCase()+`</button>
				`;
			});
			salonList.innerHTML += `
				<button onclick="addSalon()">+</button>
			`;
		});
	}
}

function subscribeToSalon(salon){
	callWithToken("POST","subscribe","salon="+salon+"&pseudo="+pseudo,
		function(msg, statut){
			getSalonList();
			sleep(500).then(function() {switchToSalon(salon)});;
		});
		
	
	
}

function switchToSalon(salon,leBouton = null){
	selectedChat = salon;
	document.getElementsByName("salonButton").forEach( e => e.style = "");
	if(leBouton){
		leBouton.style.backgroundColor = "blue";
	}
	else{
		let listButton = document.getElementById("salonList").getElementsByTagName("BUTTON");
		listButton[listButton.length - 2].style.backgroundColor = "blue";
	}
	//listUsersInSalon();
	setInterval(getMajMsg,500);
	
}

function sendMsg(){
	let monMessage = document.getElementsByName("message")[0];
	let monPseudo = pseudo;
	if(selectedChat){
		callWithToken("POST","send","pseudo="+monPseudo+"&message="+monMessage.value+"&selectedChat="+selectedChat,
			function(msg, statut){
				document.getElementsByName("message")[0].value = "";
			});
	}
}

function login(){
	let tempPseudo = document.getElementsByName("pseudo")[0].value;
	let mdp = document.getElementsByName("mdp")[0].value;
	let localStorage = window.localStorage;
	$.ajax({
		url : wsAdress,
		type : "POST",
		data : "pseudo="+tempPseudo+"&mdp="+mdp+"&operation=login",
		success : function(msg, statut){
			console.log(msg);
			rep = JSON.parse(msg);
			if(rep["errMsg"]){
				console.log(rep["errMsg"]);
			}
			else if(rep["data"] && rep["data"]["token"]){
				pseudo = tempPseudo;
				localStorage.setItem("token",rep["data"]["token"])
				switchDisplay();
			}
		},
		error : function(msg, statut, err){
			console.log(msg);
		},
	});
}


function callWithToken(pType,pOperation,pData,pFunction){
	let localStorage = window.localStorage;
	//console.log("Calling operation " + pOperation + " with token : " + localStorage.getItem("token"));
	$.ajax({
		url : wsAdress,
		type : pType,
		data : pData + "&operation="+ pOperation +"&token=" + localStorage.getItem("token"),
		success: function(msg, statut){
			let parsedRep;
			try{
				parsedRep = JSON.parse(msg);
			}catch(e){
				console.log(msg);
			}
			if(parsedRep.errMsg){
				//handle error
				console.log(msg);
			}
			else if(parsedRep.token){
				localStorage.setItem("token",parsedRep["token"]);
				pFunction(parsedRep["data"],statut);
			}
			else{
				console.log("Something is FUBAR");
			}
		}
	});
}

function inviteUserToSalon() {
    if (pseudo) {
        callWithToken("GET", "listSalon", "pseudo=" + pseudo, function (msg, statut) {
            let salons = msg;
            
            if (salons.length === 0) {
                alert("Aucun salon disponible.");
                return;
            }

            let salonListText = "Liste des salons :\n";
            salons.forEach((salon, index) => {
                salonListText += (index + 1) + ". " + salon + "\n";
            });

            let salonChoice = parseInt(window.prompt(salonListText + "Choisissez un salon (numéro) :"));
            let selectedSalon = salons[salonChoice - 1];

            if (!selectedSalon) {
                alert("Salon invalide.");
                return;
            }

            let users = getUsers();
            let userListText = "Liste des utilisateurs :\n";
            users.forEach((userId, index) => {
                userListText += (index + 1) + ". " + userId + "\n";
            });

            let userChoice = parseInt(window.prompt(userListText + "Choisissez un utilisateur à inviter (numéro) :"));
            let selectedUserId = users[userChoice - 1];

            if (!selectedUserId) {
                alert("Utilisateur invalide.");
                return;
            }

            // Envoi de la requête pour l'invitation
            callWithToken("POST", "invite", "salon=" + selectedSalon + "&user_id=" + selectedUserId, function (msg, statut) {
                if (msg.errMsg) {
                    alert("Erreur : " + msg.errMsg);
                } else {
                    alert("L'utilisateur avec ID " + selectedUserId + " a été invité au salon " + selectedSalon + " !");
                }
            });
        });
    } else {
        alert("Vous devez être connecté pour inviter des utilisateurs.");
    }
}


// Fonction simulée pour récupérer la liste des utilisateurs
function getUsers() {
    let users = [];
    $.ajax({
        url: 'scripts/users.json', // Spécifie le chemin vers ton fichier users.json
        dataType: 'json',
        async: false, // Pour s'assurer que l'appel est terminé avant de continuer
        success: function(data) {
            for (let userId in data) {
                if (data.hasOwnProperty(userId)) {
                    users.push(userId); // On n'ajoute que le userId
                }
            }
        },
        error: function() {
            console.error("Erreur lors du chargement des utilisateurs.");
        }
    });
    return users;
}

/*function listUsersInSalon() {
    if (!selectedChat) {
        alert("Aucun salon sélectionné.");
        return;
    }

    if (pseudo) {
        // Utilisation de callWithToken pour récupérer la liste des utilisateurs dans le salon sélectionné
        callWithToken("POST", "listUsersInSalon", "salon=" + selectedChat, function (msg, statut) {
            console.log("Réponse reçue de callWithToken", msg);  // Ajoute un log pour vérifier la réponse

            if (msg.errMsg) {
                alert("Erreur : " + msg.errMsg);
            } else if (msg.users && msg.users.length > 0) {
                // Sélectionne la div où afficher la liste des utilisateurs
                const usersDiv = document.getElementById('usersInSalonDiv');
                const usersList = document.getElementById('usersList');
                
                // Réinitialise le contenu précédent
                usersList.innerHTML = "";

                // Construit la liste d'utilisateurs dans le salon sélectionné
                msg.users.forEach((userId, index) => {
                    const listItem = document.createElement('li');
                    listItem.textContent = (index + 1) + ". " + userId;
                    usersList.appendChild(listItem);
                });

                // Affiche la div contenant la liste des utilisateurs
                usersDiv.style.display = "block";
            } else {
                alert("Aucun utilisateur trouvé dans ce salon.");
            }
        });
    } else {
        alert("Vous devez être connecté pour voir les utilisateurs dans ce salon.");
    }
}*/










//Tech function

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function decodeBase64URL(input) {
	// Replace non-url compatible chars with base64 standard chars
	input = input
		.replace(/-/g, '+')
		.replace(/_/g, '/');

	// Pad out with standard base64 required padding characters
	var pad = input.length % 4;
	if(pad) {
	  if(pad === 1) {
		throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
	  }
	  input += new Array(5-pad).join('=');
	}

	return input;
}

function decodeTokenData(input){
	return JSON.parse(atob(decodeBase64URL(input.split(".")[1])));
}