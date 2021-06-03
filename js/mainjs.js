// Drawer Active

$(function () {
    $('body#page-user-profile .list-group-item[data-key=profile]').addClass('active');
    $('body#page-user-editadvanced .list-group-item[data-key=profile]').addClass('active');
    $('body#page-grade-report-overview-index .list-group-item[data-key=profile]').addClass('active');
    // home
    $('body[id*=page-course] .list-group-item[data-key=home]').addClass('active');
    $('body.pagelayout-incourse .list-group-item[data-key=home]').addClass('active');
    $('body#page-grade-report-user-index .list-group-item[data-key=home]').addClass('active');
    // admin
    $('body[id*=page-admin] .list-group-item[data-key=sitesettings]').addClass('active');
    $('body.pagelayout-admin .list-group-item[data-key=sitesettings]').addClass('active');
    $('body[id*=page-blocks-coupon] .list-group-item[data-key=sitesettings]').addClass('active');
    $('body[id*=page-course] .list-group-item[data-key=sitesettings]').removeClass('active');
    $('#page-user-editadvanced .list-group-item[data-key=sitesettings]').removeClass('active');
    // grades
    $('#page-grade-report-overview-index .list-group-item[data-key=profile]').removeClass('active');
    $('#page-grade-report-overview-index .list-group-item[data-key=maingrades]').addClass('active');
});

// My index courses

$(function () {
    $(".courses .coursebox .content .courseimage").each(function () {
        $(this).prependTo($(this).parent().parent());
    });
});

// Menú interior curso

$(function () {
    $("body[id*='page-course-view'].path-user #page .menu-course li a.name").removeClass('active');
    $("body[id*='page-course-view'].path-user #page .menu-course li a.participants").addClass('active');
    $("body#page-grade-report-user-index #page .menu-course li a.name").removeClass('active');
    $("body#page-grade-report-user-index #page .menu-course li a.grades").addClass('active');
});

$(function() {
    // Eliminar cuadros perfil
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Actividad de accesos)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Detalles del curso)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Detalles de usuario)').parent().parent().remove();
    // INGLÉS - Eliminar cuadros perfil
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Login activity)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Course details)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(User details)').parent().parent().remove();
    // FRANCÉS - Eliminar cuadros perfil
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Informations de connexion)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Informations détaillées du cours)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Informations détaillées)').parent().parent().remove();
    // Portugués - Eliminar cuadros perfil
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(detalhes)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Informação sobre disciplinas)').parent().parent().remove();
    $('body.pagelayout-mypublic div[role=main] .userprofile .profile_tree h3:contains(Atividade de autenticação)').parent().parent().remove();
})
