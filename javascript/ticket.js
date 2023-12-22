//Chat sidebar functions


function openNav() {
    document.getElementById("mySidebar").style.width = "30vw";
}

function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
}

var messageBody = document.querySelector('#Messages');
messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;

