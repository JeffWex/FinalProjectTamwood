document.querySelector("form").addEventListener("submit",function(event){
    event.preventDefault();
    debugger;
    let data = new FormData(event.target);
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST","http://localhost/php/LibrarySystem/BackEnd/login.php");
    xhttp.send(data);
    xhttp.onload = function(){
        debugger;
        console.log(xhttp.responseText);
        let $type = xhttp.responseText[0];
        let ids = xhttp.responseText[15];
        switch($type){
            case "A":
                location.replace("./Adm_book.html");
                break;
            case "C":
                location.replace("./customer.html");
                break;
            case "S":
                location.replace("./Adm_book.html");
        }
        sessionStorage.setItem("cid",ids);
    }
})

