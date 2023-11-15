function chercheUn(){
    alert("Id changé");
}

var chIdClient = document.getElementById("pk");
chIdClient.addEventListener('change', chercheUn, false);