$(document).ready(function(){
	$("button[action='user']").on("click",function(){
		$("#formUser").validate({
			rules:
			{
				name: {
					required: true,
					minlength: 2,
					maxlength: 191
				},

				lastname: {
					required: true,
					minlength: 2,
					maxlength: 191
				},

				email: {
					required: true,
					email: true,
					minlength: 8,
					maxlength: 191
				},

				password: {
					required: true,
					minlength: 8,
					maxlength: 40
				},

				password_confirmation: { 
					equalTo: "#password",
					minlength: 8,
					maxlength: 40
				}
			},
			messages:
			{
				name: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				},

				lastname: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				},

				email: {
					email: 'Introduce una dirección de correo valida.',
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				},

				password: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				},

				password_confirmation: { 
					equalTo: 'Los datos ingresados no coinciden.',
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				}
			}
		});
	});

	$("button[action='gamer']").on("click",function(){
		$("#formGamer").validate({
			rules:
			{
				name: {
					required: true,
					minlength: 2,
					maxlength: 191
				},

				lastname: {
					required: true,
					minlength: 2,
					maxlength: 191
				}
			},
			messages:
			{
				name: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				},

				lastname: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				}
			}
		});
	});

	$("button[action='club']").on("click",function(){
		$("#formClub").validate({
			rules:
			{
				name: {
					required: true,
					minlength: 2,
					maxlength: 191
				}
			},
			messages:
			{
				name: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				}
			}
		});
	});

	$("button[action='game']").on("click",function(){
		$("#formGame").validate({
			rules:
			{
				points1: {
					required: true,
					min: 0,
					max: 2
				},

				points2: {
					required: true,
					min: 0,
					max: 2
				}
			},
			messages:
			{
				points1: {
					min: 'Escribe un valor mayor o igual a {0}.',
					max: 'Escribe un valor menor o igual a {0}.'
				},

				points2: {
					min: 'Escribe un valor mayor o igual a {0}.',
					max: 'Escribe un valor menor o igual a {0}.'
				}
			}
		});
	});

	$("button[action='tournament']").on("click",function(){
		$("#formTournament").validate().destroy();
        var numberGroup=$('#maxGroup').val();
        var minimo=numberGroup*3, maximo=numberGroup*6;
		$("#formTournament").validate({
			rules:
			{
				name: {
					required: true,
					minlength: 2,
					maxlength: 191
				},

				type: {
					required: true
				},

				groups: {
					required: true,
					min: 1,
					max: 6
				},

				couples: {
					required: true,
					min: minimo,
					max: maximo
				}
			},
			messages:
			{
				name: {
					minlength: 'Escribe mínimo {0} caracteres.',
					maxlength: 'Escribe máximo {0} caracteres.'
				},

				type: {
					required: 'Seleccione una opción.'
				},

				groups: {
					min: 'Escribe un valor mayor o igual a {0}.',
					max: 'Escribe un valor menor o igual a {0}.'
				},

				couples: {
					min: 'Escribe un valor mayor o igual a {0}.',
					max: 'Escribe un valor menor o igual a {0}.'
				}
			}
		});
	});
});