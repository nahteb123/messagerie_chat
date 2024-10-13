<html>
	<head>
		<title>Wishcord</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		
		<style>
			*{
				color:#DDDDDD;
			}
			body{
				background-color:#333333;
			}
			button{
				background-color:#333333;
				width:95%;
				margin: 0 2.5%;
			}
			input{
				background-color:#151515;
			}
			#chatDiv{
				display:none;
				margin-bottom:10px;
				
				height:60%;
			}
			#salonList{
				border: 1px solid;
				width:50px;
				min-height:60%;
				padding:5px;
				flex-direction: column;
				overflow-y:scroll;
				overflow-x:hidden;
			}
			#salonList > *{
				display:block;
				margin-top:5px;
				border-radius:50%;
				font-size:16px;
				background-color:grey;
				height:50px;
				width:50px;
			}
			#chat{
				flex-grow:4;
				resize:none;
				background-color:#151515;
				
			}
			#messageDiv > input[type='text']{
				width : 100%;
				height : 40px;
				margin : 5px 0;
				background-color:#151515;
			}
			#decoButton{
				min-width: 15%;
				margin-left: 50px;
				max-width: 15%;
			}
		</style>
	</head>
	<body>
		<p id="connectStatus">Non connecté</p>
		<div id="chatDiv">
			<div id="salonList">
			</div>
			<textarea disabled style="" id="chat"></textarea>
		</div>
		<div id="pseudoDiv">
			<label>Pseudo : </label>
			<input type="text" name="pseudo"/>
			
			<label>Mdp : </label>
			<input type="secret" name="mdp"/>
			<button style ="margin-top:10px;" onclick="login()">Se connecter</button>
			<button style ="margin-top:5px;" onclick="register()">S'inscrire</button>
		</div>
		<div id="messageDiv" style="display:none">
			<label>Message : </label>
			<input type="text" name="message" />
			<button onclick="sendMsg()">Envoyer</button>
		</div>
		<script src="./monjs.js"></script>
	</body>
</html>
