/* Retorna o id do usuário da url na barra de navegação */
jQuery.fn.getUser = function() { 
  var regex = new RegExp("[\\?&]id=([^&#]*)"); 
  var regid = regex.exec(window.location.search);   
  return (regid === null) ? 0 : parseInt(decodeURIComponent(regid[1].replace(/\+/g, " ")));
}; 

/* Estrutura as recomendações para exibir na tela (HTML) */
jQuery.fn.toHTML = function(recommender) {
  var html = "";  
  $.each(recommender.objects, function(i, rec) {
    if (i != 0) {
      html += "<hr />";
    }
    html += "<h3><a href=\""+rec.location+"\" class=\"merlot_recommender_link\">"+rec.title+"</a></h3>";
    html += "<p align=\"justify\">"+rec.description+"</p>";
    html += "<img src=\"/merlot/images/apricotstars/"+rec.value+".png\" alt=\""+rec.value+" rating\" style=\"border:0\">";
  });
  html += "<hr />Errors:";
  html += "<ul>";
  html += "<li>MAE = "+recommender.mae+"</li>";
  html += "<li>RMS = "+recommender.rms+"</li>";
  html += "</ul>";
  return html;
};

jQuery.fn.closeTile = function() {
  var tile = $(this).parent();
  tile.delay(5000).fadeOut(500, function() {
    /* remove a largura da tile para a página centralizar */
    tile.parent().css({
      "min-width": function( index, value ) {
        return parseFloat(value)
         - parseFloat(tile.css("width"))
         - parseFloat(tile.css("margin-left"))
         - parseFloat(tile.css("margin-right"));
      }
    });
    /* centraliza as tiles absolutas de baixo */
    $(".member_bottom_tile").css({
      "left": function( index, value ) {
        return parseFloat(value) 
         + (parseFloat(tile.css("width"))/2)
         + (parseFloat(tile.css("margin-left"))/2)
         + (parseFloat(tile.css("margin-right"))/2);
      }
    });
    /* Caso feche 2 tiles */
    if ($(".merlot_recommender_tile").length == 1) {
      $(".member_bottom_tile").css({
        "left": function( index, value ) {
          return parseFloat(value) 
           - parseFloat(tile.css("margin-left"))
           - parseFloat(tile.css("margin-right"));
        }
      });
    }
    /* remove a tile */
    tile.remove();
  });
  return this;
};

/* Realiza as recomendações, comunicando-se com o servidor */
jQuery.fn.recommender = function(options) { 
  var self = this;
  /* Servidor local */
  var server = "http://127.0.0.1:8080/";
  /* Requisição GET para o servidor com resposta em json */
  $.get(server, options, function (data) {
    /* Analisa a resposta dada pelo servidor */ 
    switch (data.action) {
      /* Mostra na tela as recomendações */
      case "recommender":
        /* Se houver recomendações */
        if (data.recommender.objects.length > 0) {
          self.slideUp(500, function() {
            $(this)
              .html($(this).toHTML(data.recommender))
              .slideDown(500);
          });
        }
        break;
      /* Exibe no console mensagens de erro */
      case "error":
        /* mostra a mensagem de erro */
        self
          .html("<p style=\"color: red; text-align: center\">"+data.error+"</p>")
          .closeTile();
        break;
      /* Se a resposta não for reconhecida exibe no console */
      default:
        console.log(JSON.stringify(data));
    }
  }, "json").fail(function() {
    self
      .html("<p style=\"color: red; text-align: center\">"+data.error+"</p>")
      .closeTile();
  });
  return this; 
};  

/* Quando a página carregar */
$(function() {
  /* Pega o id do usuário */
  var user = $(this).getUser();
  if (user != 0) {
    /* insere as caixas de recomendações */
    var html = "";  
    html += "<div id=\"merlot_recommender_tile1\" class=\"merlot_recommender_tile member_tile\">";
    html += "<h3><span class=\"member_tile_header_text\">Specific Recommendations</span></h3>";
    html += "<div class=\"member_tile_body\"><p align=\"center\">loading . . .</p></div>";
    html += "</div>";
    html += "<div id=\"merlot_recommender_tile2\" class=\"merlot_recommender_tile member_tile\">";
    html += "<h3><span class=\"member_tile_header_text\">General Recommendations</span></h3>";
    html += "<div class=\"member_tile_body\"><p align=\"center\">loading . . .</p></div>";
    html += "</div>";
    $("#main_div")
      .html("<div id=\"merlot_recommender_wrapper\">"+$("#main_div").html()+"</div>")
      .append(html);
    /* realiza as recomendações */
    $(".member_tile_body", "#merlot_recommender_tile1")
      .recommender({action: "recommender", type: "discipline", iduser: user}); 
    $(".member_tile_body", "#merlot_recommender_tile2")
      .recommender({action: "recommender", type: "general", iduser: user});
  }
});