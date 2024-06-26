$(document).ready(function () {
    debugger;
    loadUsers();

});
const buttonPoper = (tr) => {
    debugger;
    let td = document.createElement("td");
    tr.append(td);
    return tr;
}

const tablePoper = (data) => {
    debugger;
    document.querySelector("table").removeAttribute("style");
    for (let m of data) {
        debugger;
        let tr = document.createElement("tr");

        for (let p in m) {
            let td = document.createElement("td");
            td.innerText = m[p];

            if (p == "new_staff" && td.innerText == "0") {
                td.innerText = "False";
            } else if (p == "new_staff" && td.innerText == "1") {
                td.innerText = "True";
            }

            tr.append(td);
        }
        debugger;
        // Add a button to toggle the boolean value
        let toggleButton = document.createElement("button");
        toggleButton.innerText = "Approval";
        toggleButton.addEventListener("click", function () {
            toggleBooleanValue(tr, m.id); // Replace "id" with the actual identifier for your data
        });

        document.querySelector("tbody").append(buttonPoper(tr))
        // tr.append(buttonPoper(tr));
        tr.append(toggleButton);
        document.querySelector("tbody").append(tr);
    }
};

const toggleBooleanValue = (row, id) => {
    debugger;
    let newValue = row.querySelectorAll("td")[6].innerText === "False" ? "True" : "False"; 

    // Update the UI
    row.querySelectorAll("td")[6].innerText = newValue === "True" ? "True" : "False";

    newValue = newValue === "True" ? 1 : 0;

    // Send the updated value to PHP via Ajax
    // You need to implement this part based on your PHP endpoint
    // Example using fetch:
    fetch('http://localhost/php/LibrarySystem/BackEnd/Adm_userReport.php', {
        method: 'POST',
        body: JSON.stringify({ id: id, new_staff: newValue }),
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        // Handle the response if needed
        console.log(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
};

// const tablePoper = (data) => {
//     debugger;
//     document.querySelector("table").removeAttribute("style");
//     for (let m of data) {
//         debugger;
//         let tr = document.createElement("tr");
//         for (let p in m) {
//             let td = document.createElement("td");
//             td.innerText = m[p];
//             if(p == "new_staff" && td.innerText == "0"){
//                 td.innerText = "False";
//             }
//             else if(p == "new_staff" && td.innerText == "1"){
//                 td.innerText = "True";
//             }
//             tr.append(td);
//         }
//         document.querySelector("tbody").append(buttonPoper(tr));
//     }
// }

function loadUsers() {
    debugger;
    $.ajax({
        method: 'GET',
        url: 'http://localhost/php/LibrarySystem/BackEnd/Adm_userReport.php',
        success: function (response) {
            debugger;
            console.log(response);
            const userList = $('#userTable');
            userList.empty();

            tablePoper(response);
        },
        error: function (error) {
            console.log(error);
        },
    });
}