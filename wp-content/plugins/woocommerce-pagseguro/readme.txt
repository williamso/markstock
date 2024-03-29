=== WooCommerce PagSeguro ===
Contributors: claudiosanches, Gabriel Reguly
Donate link: http://claudiosmweb.com/doacoes/
Tags: woocommerce, pagseguro, payment
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds PagSeguro gateway to the WooCommerce plugin

== Description ==

### Add PagSeguro gateway to WooCommerce ###

This plugin adds PagSeguro gateway to WooCommerce.

Please notice that WooCommerce must be installed and active.

= Contribute =

You can contribute to the source code in our [GitHub](https://github.com/claudiosmweb/woocommerce-pagseguro) page.

### Descrição em Português: ###

Adicione o PagSeguro como método de pagamento em sua loja WooCommerce.

[PagSeguro](https://pagseguro.uol.com.br/) é um método de pagamento brasileiro desenvolvido pela UOL.

O plugin WooCommerce PagSeguro foi desenvolvido sem nenhum incentivo do PagSeguro ou da UOL. Nenhum dos desenvolvedores deste plugin possuem vínculos com estas duas empresas.

Este plugin desenvolvido a partir da [documentação oficial do PagSeguro](https://pagseguro.uol.com.br/v2/guia-de-integracao/visao-geral.html).

= Instalação: =

Confira o nosso guia de instalação e configuração do PagSeguro na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-pagseguro/installation/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-pagseguro/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-pagseguro) (apenas em inglês).
* Ou entre em contato com os desenvolvedores do plugin em nossa [página](http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/).

= Coloborar =

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/claudiosmweb/woocommerce-pagseguro).

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Payment Gateways, choose PagSeguro and fill in your PagSeguro Email and Token.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

É necessário possuir uma conta no [PagSeguro](http://pagseguro.uol.com.br/) e ter instalado o [WooCommerce](http://wordpress.org/extend/plugins/woocommerce/).

= Configurações no PagSeguro: =

É necessário configurar a página de redirecionamento no PagSeguro.
Você pode fazer isso em "Integrações" > "[Página de redirecionamento](https://pagseguro.uol.com.br/integracao/pagina-de-redirecionamento.jhtml)":

Ative a opção de "Página fixa de redirecionamento" e configure ela como por exemplo:

    http://seusite.com/finalizar-compra/pedido-recebido/

Habilite também o retorno automático de dados;
Deve ser ativado em "Integrações" > "[ Retorno automático de dados](https://pagseguro.uol.com.br/integracao/retorno-automatico-de-dados.jhtml)".

Marque ativado e adicione a url como por exemplo:

    http://seusite.com/?wc-api=WC_PagSeguro_Gateway

No momento o plugin funciona apenas com a integração simples. Desta forma é necessário desativar os pagamentos via API.
Você pode fazer isso em "Integrações" > "[Pagamentos via API](https://pagseguro.uol.com.br/integracao/pagamentos-via-api.jhtml)".

Com estas configurações a sua conta no PagSeguro estará pronta para receber os pagamentos de sua loja.

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Configurações" > "Portais de pagamento"  > "PagSeguro".

Habilite o PagSeguro, adicione o seu e-mail e o token do PagSeguro.

Utilizamos o token para validar o retorno automático de dados.
Você pode conseguir um token no PagSeguro em "Integrações" > "[Token de Segurança](https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml)".

= Configurações no WooCommerce =

No WooCommerce 2.0 ou superior existe uma opção para cancelar a compra e liberar o estoque depois de alguns minutos.

Esta opção não funciona muito bem com o PagSeguro, pois pagamentos por boleto bancário pode demorar até 48 horas para serem validados.

Para corrigir isso é necessário ir em "WooCommerce" > "Configurações" > "Inventário" e limpar (deixe em branco) o valor da opção **Manter Estoque (minutos)**.

Pronto, sua loja já pode receber pagamentos pelo PagSeguro.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce installed and active
* Only one account on [PagSeguro](http://pagseguro.uol.com.br/ "PagSeguro").

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce.
* Possuir uma conta no PagSeguro.
* Gerar um token de segurança no PagSeguro.
* Habilitar o Retorno automático de dados no PagSeguro.
* Desativar os pagamentos via API.

= Como funciona o PagSeguro? =

* Saiba mais em [PagSeguro - Como funciona](https://pagseguro.uol.com.br/para_seu_negocio/como_funciona.jhtml).
* Ou acesse a [FAQ do PagSeguro](https://pagseguro.uol.com.br/atendimento/perguntas_frequentes.jhtml).

= PagSeguro recebe pagamentos de quais países? =

No momento o PagSeguro recebe pagamentos apenas do Brasil.

Configuramos o plugin para receber pagamentos apenas de usuários que selecionarem o Brasil nas informações de pagamento durante o checkout.

= Quais são os meios de pagamento que o plugin aceita? =

São aceitos todos os meios de pagamentos que o PagSeguro disponibiliza.
Entretanto você precisa ativa-los na sua conta no PagSeguro.

Confira os meios de pagamento em [PagSeguro - Meios de Pagamento e Parcelamento](https://pagseguro.uol.com.br/para_voce/meios_de_pagamento_e_parcelamento.jhtml#rmcl).

= Quais são as taxas de transações que o PagSeguro cobra? =

Para estas informações consulte: [PagSeguro - Taxas e Tarifas](https://pagseguro.uol.com.br/taxas_e_tarifas.jhtml).

= Quais são os limites de recebimento do PagSeguro? =

Consulte: [PagSeguro - Tabela de Limites](https://pagseguro.uol.com.br/account/limits.jhtml).

= Como que plugin faz integração com PagSeguro? =

Fazemos a integração baseada na documentação oficial do PagSeguro que pode ser encontrada em "[Guia de integração - PagSeguro](https://pagseguro.uol.com.br/v2/guia-de-integracao/visao-geral.html)".

= Instalei o plugin, mas a opção de pagamento do PagSeguro some durante o checkout. O que fiz de errado? =

Você esqueceu de selecionar o Brasil durante o cadastro no checkout.
A opção de pagamento pelo PagSeguro funciona apenas com o Brasil.

= É possível enviar os dados de "Número" e "Bairro" para o PagSeguro? =

Sim é possível, basta utilizar o plugin [WooCommerce Extra Checkout Fields for Brazil](http://wordpress.org/extend/plugins/woocommerce-extra-checkout-fields-for-brazil/).

= A compra é cancelada após alguns minutos, mesmo com o pedido sendo pago, como resolvo isso? =

Para resolver este problema vá até "WooCommerce" > "Configurações" > "Inventário" e limpe (deixe em branco) o valor da opção **Manter Estoque (minutos)**.

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Entre em contato [clicando aqui](http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/).

== Screenshots ==

1. Settings page.
2. Checkout page.

== Changelog ==

= 1.6.0 - 26/07/2013 =

* Adicionado o filtro `woocommerce_pagseguro_icon` para troca do ícone do método de pagamento.
* Melhoria no filtro `woocommerce_pagseguro_args`, agora ele aceita o objeto `WC_Order` no lugar do ID.
* Melhoria nas opções do plugin.
* Melhoria nas mensagens de status do pedido.

= 1.5.0 - 24/06/2013 =

* Adicionado link de `Configurações` na página de plugins.
* Melhorias no código.
* Adicionado suporte para WooCommerce 2.1.
* Adicionado o ID da compra no filtro `woocommerce_pagseguro_args`.

= 1.4 - 02/04/2013 =

* Correção do retorno automático de dados na versão 2.0.0 ou superior do WooCommerce.

= 1.3.4 - 06/03/2013 =

* Corrigida a compatibilidade com WooCommerce 2.0.0 ou mais recente.

= 1.3.3 - 08/02/2013 =

* Corrigido o hook responsavel por salvar as opções para a versão 2.0 RC do WooCommerce.

= 1.3.2 - 08/02/2013 =

* Plugin corrigido para a versão 2.0 do WooCommerce.

= 1.3.1 - 08/12/2012 =

* Melhoria no método que atualiza o status do pedido.
* Correção da quantidade de caracteres das descrição dos produtos no PagSeguro.

= 1.3 - 30/11/2012 =

* Adicionada opção para logs de erro.
* Adiciona opção para validar ou não endereço (quando ativo força cliente a informar os dados corretamente e ir direto para a página de pagamento do PagSeguro).

= 1.2.2 - 19/11/2012 =

* Corrigido problema com cupons de desconto (descontos no carrinho).

= 1.2.1 =

* Corrigido o problema de produto com a descrição/nome muito grande no PagSeguro.

= 1.2 =

* Removida a classe do retorno automático que usava cURL em favor da função wp_remote_post().
* Otimizado o retorno automático de dados.

= 1.1.1 =

* Tradução revisada.
* Melhorada a integração de retorno automático para o status de "Aguardando pagamento".

= 1.1 =

* Adicionado retorno automático de dados.
* Melhorado o sistema de notificações.
* Adicionada classe para validar o retorno automático de dados.
* Adicionado campo de token nas configurações do plugin (necessário para o retorno automático).

= 1.0.2 =

* Adicionada função para abater estoque automáticamente quando o cliente vai para o PagSeguro.

= 1.0.1 =

* Correção para impedir que o portal de pagamento seja habilitado se a conta de email do PagSeguro estiver em branco

= 1.0 =

* Versão incial do plugin.

== Upgrade Notice ==

= 1.6.0 =

* Adicionado o filtro `woocommerce_pagseguro_icon` para troca do ícone do método de pagamento.
* Melhoria no filtro `woocommerce_pagseguro_args`, agora ele aceita o objeto `WC_Order` no lugar do ID.
* Melhoria nas opções do plugin.
* Melhoria nas mensagens de status do pedido.

== License ==

WooCommerce PagSeguro is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce PagSeguro is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce PagSeguro. If not, see <http://www.gnu.org/licenses/>.
