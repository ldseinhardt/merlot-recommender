### Autores
* Gustavo Magalhaes <glmagalhaes.mail@gmail.com>
* Luan Einhardt <ldseinhardt@gmail.com>
* Marcelo Leão <mlcfay@inf.ufpel.edu.br>

### Casos de teste
* [http://www.merlot.org/merlot/viewMember.htm?id=8390](http://www.merlot.org/merlot/viewMember.htm?id=8390)
* [http://www.merlot.org/merlot/viewMember.htm?id=336573](http://www.merlot.org/merlot/viewMember.htm?id=336573)
* [http://www.merlot.org/merlot/viewMember.htm?id=270281](http://www.merlot.org/merlot/viewMember.htm?id=270281)
* [http://www.merlot.org/merlot/viewMember.htm?id=15410](http://www.merlot.org/merlot/viewMember.htm?id=15410)

### Configurações
* Plugin:
 * servidor (`http://127.0.0.1:8080`)
 * permissões (`http://127.0.0.1:8080/*`)
 * Instalação/Testes:
    * Google Chrome:
      * Ir em `chrome://extensions`
      * Habilitar `Modo de desenvolvedor`
      * Clicar em `Carregar extensão expandida` e selecionar a pasta `plugin`
    * Mozilla Firefox:
      * Deve-se instalar o SDK (disponível [aqui](https://developer.mozilla.org/en-US/Add-ons/SDK/Tutorials/Installation))
      * `cd plugin`
      * `cfx run` (para rodar em "modo de teste")
      * `cfx xpi` (para empacotar o plugin)
        * instalando o plugin em formato `xpi`:
          * Ir em `about:addons`
          * Selecionar a opção `Instalar de um arquivo...`, disponível no botão de configurações (ícone de engrenagem)
          * Aceitar a instalação e ativar ela caso necessário (dado pela opção Ativar/Desativar)
* Webservice:
 * PHP >= 5.4
    * Windows:
      * Adicionar o path do java nas variáveis de ambiente
    * Linux:
      * Para executar: `./webservice.sh`
 * MySQL:
    * configurações de login para conexão `webservice/settings.php`
    * instanciar a base de dados
    * importar a tabela `app_errors` em `webservice/Merlot-Recommender.sql`