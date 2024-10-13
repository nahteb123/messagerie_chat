<html>
	<head>
		<title>Messagerie asynchrone</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link href="./chat.css" rel="stylesheet" type="text/css" />
		
		
	</head>
	<body id="body">
    <p id="connectStatus" style="display:none">Non connecté</p>
    <div id="chatDiv">
        <div id="salonList">
        </div>
        <textarea disabled style="" id="chat"></textarea>
    </div>
    <div id="pseudoDiv">
		<div id="connexion">
			<label class="label_connexion">Pseudo : </label>
        	<input type="text" class="input_connexion" name="pseudo"/>
        	<label class="label_connexion">Mot de passe : </label>
        	<input type="password" class="input_connexion" name="mdp"/>
		</div> 
		<div class="boutons_login">
			<button class="boutton_register" onclick="register()">S'inscrire</button>
       		<button class="boutton_connexion" onclick="login()">Se connecter</button>
		</div>
		<div>
            <button onclick="resetPassword()">Réinitialiser le mot de passe</button>
         </div>
       
    </div>
    
    <!--<div id="usersInSalonDiv" style="display:none;">
        <h3>Utilisateurs dans le salon :</h3>
        <ul id="usersList">
           
        </ul>
    </div>-->

    <div id="messageDiv" style="display:none">
        <label>Message : </label>
        <input type="text" name="message" />
        <button onclick="sendMsg()">Envoyer</button>
    </div>

    <script src="./monjs.js"></script>
</body>

</html>
