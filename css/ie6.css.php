<? include "../../../View/config.php"; ?>
<style>

/* Página Consulta */
#menu_consulta a{
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e7e7e7',GradientType=0);
}
#menu_consulta a.ativo, #menu_consulta a.active{
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0); 
}
#menu_consulta .icon.consignante{
	_background: transparent;
	_filter: progid:DXImageTransform.Microsoft.AlphaImageLoader (src='<?= APP_IMG_URL; ?>consignante.png', sizingMethod='size');
}
#menu_consulta .icon.agencia{
	_background: transparent;
	_filter: progid:DXImageTransform.Microsoft.AlphaImageLoader (src='<?= APP_IMG_URL; ?>agencia.png', sizingMethod='size');
}
#menu_consulta .icon.correspondente{
	_background: transparent;
	_filter: progid:DXImageTransform.Microsoft.AlphaImageLoader (src='<?= APP_IMG_URL; ?>correspondente.png', sizingMethod='size');
}

#conteudo,
#footer, #studioFooter{
	_width: 1020px;
}
#header, #div_topo,
#footer, #studioFooter{
	_width: 1020px;
}

#janela1 input{
	_border: none;
}

#header, #div_topo{
	*position: relative;
	*border-bottom: none;
}

#user{
	*position: relative;
	*float: right;
	_float: none;
	_margin-left: 900px;
	_margin-top: 0;
}
	#user ul{
		_position: absolute;
		_padding-top: 7px;
		_top: 29px;
		_right: 0;
	}
		#user ul ul{
			_position: relative;
			_padding-top: 0;
			_top: 0;
			_display: block;
		}
			#servidor #user.ie_user ul,
			#servidor #user.ie_user ul li a{
				_width: 240px;
				_right: 0;
			}
			#servidor #user.ie_user li.submenu2 a.seleciona_matricula:hover{
				_background-color: transparent;
			}

#nav li a .sprite{
	_display:none;
	_background: none;
}
#nav{
	_top: 37px;
	_height: 15px;
	_font-size:12px;
}
	#nav li a{
		*width: 100%; /* IE 7 */
		*height: 100%; /* IE 7 */
	}
	#nav li.consultas{
		_width: 56px;
	}
	#nav li.cadastros{
		_width: 55px;
	}
	#nav li.habilitar{
		_width: 48px;
	}
	#nav li.administracao{
		_width: 83px;
	}
	#nav li.fechamento{
		_width: 69px;
	}
	#nav li.averbar_margem{
		_width: 95px;
	}
	#nav li.digitacao{
		_width: 53px;
	}
	#nav li.solicitacoes{
		_width: 70px;
	}
	#nav li.servfacil{
		_width: 50px;
	}
	#nav li.simulador{
		_width: 50px;
	}
	#nav li.ajuda{
		_width: 32px;
	}
		#nav li ul{
			_top: 20px;
		}
			#nav li li a{
				*width: 160px;
			}
			#nav li.administracao li a{
				*width: 200px;
			}
				#nav li.administracao li ul.submenu2 a{
					_width: 45px;
				}
			#nav li.digitacao li a{
				*width: 70px;
			}
			#nav li.fechamento li a{
				*width: 270px;
			}
			#nav li.solicitacoes li a{
				*width: 85px;
			}
				#nav li.solicitacoes li:hover ul.submenu2 a{
					*width: 105px;
				}
			#nav li li:hover ul.submenu2 a{
				*width: 80px;
			}

			#agencia #nav li.consultas li a,
			#correspondente #nav li.consultas li a,
			#filial_correspondente #nav li.consultas li a{
				*width: 115px;
			}
			#averbador #nav li.fechamento li a,
			#consignataria #nav li.fechamento li a{
				*width: 170px;
			}
			#consignataria #nav li.cadastros li a{
				*width: 70px;
			}
			#servidor #nav li.servfacil li a{
				*width: 110px;
			}
			#servidor #nav li.simulador li a{
				*width: 110px;
			}

#conteudo{
	*padding-top: 10px;
	_width: 1020px;
}
	#conteudo h2.title{
		_width: 95%;
	}

#tabelausuario{
	*width: 1000px;
}

.right.leftBorder{
	margin-top: -35px;
}

#content table.pad10, #conteudo table.pad10{
	font-size: 11px;
}
	#content h3 .canto, #div_campos h3 .canto,
	#div_tudo p.titleInfo .canto{
		_height: 35px;
	}
#footer, #studioFooter{
	_position: relative;
}
	#footer span, #studioFooter span{
		_display: none;
	}

#banner_home_ajuda{
	_width: 346px;
}

#sidebar_home .cx_sidebar{
	_cursor: default;
}
	#sidebar_home .cx_sidebar.mais span.icon,
	#sidebar_home .cx_sidebar.menos span.icon{
		_display: none!important;
	}

#caixa_home{
	*width: 1000px;
}

.icon_pdf{
	_background-image: url('../image/novo/pdf.gif');
}

table td {
	_padding-left: 5px;
	_padding-right: 5px;
}

#agrupamento .mais span.icon,
#tb_ListagemAgrupada .btn_expande span.icon,
#sidebar_home .cx_sidebar.mais span.icon{
	_background-image: url('../image/novo/buttons/mais.gif');
}
#agrupamento .menos span.icon,
#tb_ListagemAgrupada .btn_collapse span.icon,
#sidebar_home .cx_sidebar.menos span.icon{
	_background-image: url('../image/novo/buttons/menos.gif');
}

#agrupamento dt .leftBorder{
	*margin-top: 0;
}

.solicitacao .jform .inputPosicao{
	_margin-right: 0;
}

.icon_saldo_devedor {
	_background-image: url("../image/novo/icons/saldo-devedor.gif");
}
.icon_val_saldo {
	_background-image: url("../image/novo/icons/val-saldo.gif");
}
.icon_clock {
	_background-image: url("../image/novo/icons/clock.gif");
}

.tabela_perfil td span.ativo{
	_background: url('../image/novo/green.gif');
}
.tabela_perfil td span.inativo{
	_background: url('../image/novo/grey.gif');
}

span.user_on{
	_background: url('../image/novo/user_on.gif');
}
 span.user_off{
	_background: url('../image/novo/user_off.gif');
}

button.bt.ativar_cartao{
	_position: static;
}

button.bt2.cancelar{
	_width: 132px;
}

#infocartaoativo .bt3.cancelar,
#infocartaoativo .bt3.confirmar,
#infocartaoativo .bt3.voltar{
	*position: static;
}

#ajuda {
	*top: 15px;
	_top: 0;
}
#ajuda li a{
	_margin-left: 0px;
}
 
.ajuda .caixa_ajuda #faq dl dt span,
.ajuda .caixa_ajuda #edit_faq dl dd small{
	_background-image: url('../image/novo/help.gif');
}

.ajuda .caixa_ajuda #faq dl dt.ativo span{
	_background-image: url('../image/novo/comment.gif');
}

#resultado_consulta li{
	_float: none;
}


/* slick */
.cx-slick{
	width: 450px;
}
#slick {
	margin-bottom: 10px;
	height: 118px;
}
#slick a{
	float: left;
	margin: 9px;
	font-size: 26px;
	line-height: normal;
}
#slick a span{
	font-size: 12px;
}
</style>