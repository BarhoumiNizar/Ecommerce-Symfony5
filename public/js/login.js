$(function() {
    $("form[name=client]").submit(function(){
        if($("#client_email").val() == "") {
            $("#client_email").focus();
            $(".email_error").html("email obligatoire")
            return false;
        }
        if($("#client_password").val() == "") {
            $("#client_password").focus();
            $(".email_error").html("")
            $(".password_error").html("password obligatoire")
            return false;
        }
        return true;
    })
})