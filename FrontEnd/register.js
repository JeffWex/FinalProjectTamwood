$(document).ready(function () {
    debugger;
    $('#submit').on('click', function () {
        debugger;
        const fname = $('#firstName').val();
        const lname = $('#lastName').val();
        const mobile = $('#mobile').val();
        const email = $('#email').val();
        const password = $('#password').val();
        const user_type = $('#userType').val();
        // const new_staff = $('#file').val();
       

        $.ajax({
            url: 'http://localhost/php/LibrarySystem/BackEnd/register.php',
            method: 'POST',
            data: JSON.stringify({ fname, lname, mobile, email, password, user_type }),
            contentType: 'application/json',
            success: function (response) {
                console.log(response);
                debugger;
            },
            error: function (error) {
                console.log(error);
            },
        });
    });

});
