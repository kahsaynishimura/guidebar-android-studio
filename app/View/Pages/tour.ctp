<style>
    p{
       color: #dc1c4f; 
       font-size: 120%;
       text-indent: 20px;
    }
</style>
<div> <?php echo $this->Html->link('GuideBar Home', array('controller' => 'users', 'action' => 'login'), array('escape' => false)); ?> </div>
<h1>Tour</h1>
<p >
    O guideBAR é um sistema simples de venda de entras online.
    Para começar a vender entradas basta inserir seu token do pagseguro no cadastro do seu perfil.
</p> 

<p >
    O primeiro passo é se cadastrar no pagseguro e gerar um token de vendedor.
</p> 

<p >
    O segundo passo é efetuar o cadastro no guideBAR e inserir o token gerado no pagseguro.
    Note que é possível gerar token varias vezes, mas somente o ultimo token gerado é valido. Os tokens gerados anteriormente param de funcionar, então cuida para utilizar sempre o ultimo token gerado.
</p> 

<p >
    Agora a criação do evento no guideBAR.
    Insira os dados do evento como nome, descrição e localização, coloque também algumas imagens como o ícone do evento e álbum de atrações.
</p> 

<p >
    E essa é a parte mais esperada. Criar as entradas para a venda online. Informe o nome do ingresso ex.: Lote 1 Feminino. A quantidade de entradas que deseja vender e o valor da entrada.
</p> 

<p >
    É possível também criar as entradas para venda convencional, aquela venda de mão em mão mesmo que ainda é uma forma muito utilizada para atingir o publico alvo.
    Essas entradas são geradas com um código QR que será usado para validar as entradas com seu smartphone na hora do evento. As entradas online também tem esse código QR.
    Cada entrada gerada, sendo online ou não, vem com um código QR diferente tornando cada bilhete único e exclusivo para seu evento.
</p> 