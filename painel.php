<?php

$file = __DIR__ . "/data/products.json";

$data = json_decode(file_get_contents($file), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$data["checkoutUrl"] = $_POST["checkoutUrl"];

$sections = [];

if(isset($_POST["sections"])){

foreach($_POST["sections"] as $section){

$products = [];

if(isset($section["products"])){

foreach($section["products"] as $p){

if(trim($p["name"])=="") continue;

$products[] = [

"id" => intval($p["id"]),
"name"=>$p["name"],
"price"=>$p["price"],
"oldPrice"=>$p["oldPrice"],
"badge"=>$p["badge"],
"img"=>$p["img"],
"soldOut"=>isset($p["soldOut"])

];

}

}

if(count($products)>0){

$sections[]=[

"title"=>$section["title"],
"products"=>$products

];

}

}

}

$data["sections"]=$sections;

file_put_contents($file,json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

header("Location: painel.php?saved=1");
exit;

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<title>Painel de Produtos</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:#0f172a;
font-family:system-ui;
color:#e2e8f0;
}

.sidebar{
width:240px;
background:#020617;
height:100vh;
position:fixed;
padding:30px;
}

.sidebar h3{
color:white;
}

.main{
margin-left:260px;
padding:40px;
}

.card{
background:#1e293b;
border:none;
border-radius:14px;
box-shadow:0 10px 30px rgba(0,0,0,.3);
}

.product{
background:#334155;
padding:15px;
border-radius:10px;
margin-bottom:12px;
}

input{
background:#020617!important;
border:1px solid #334155!important;
color:white!important;
}

.preview{
height:60px;
object-fit:contain;
}

.header{
position:sticky;
top:0;
background:#0f172a;
padding:15px;
border-bottom:1px solid #334155;
z-index:999;
margin-bottom:20px;
}

</style>

</head>

<body>

<div class="sidebar">

<h3><i class="bi bi-box"></i> Produtos</h3>
<p class="text-secondary">Painel administrativo</p>

</div>

<div class="main">

<form method="POST">

<div class="header d-flex justify-content-between align-items-center">

<h4>Painel de Produtos</h4>

<button class="btn btn-success">
<i class="bi bi-save"></i> Salvar
</button>

</div>

<?php if(isset($_GET["saved"])): ?>

<div class="alert alert-success">
Alterações salvas com sucesso
</div>

<?php endif; ?>

<div class="card p-4 mb-4">

<label class="mb-2">Checkout Link</label>

<input class="form-control"
name="checkoutUrl"
value="<?=htmlspecialchars($data["checkoutUrl"])?>">

</div>

<div id="sections">

<?php foreach($data["sections"] as $si=>$section): ?>

<div class="card p-4 mb-4 section">

<div class="d-flex justify-content-between mb-3">

<input class="form-control w-50"
name="sections[<?=$si?>][title]"
value="<?=htmlspecialchars($section["title"])?>">

<button type="button"
class="btn btn-danger remove-section">
<i class="bi bi-trash"></i>
</button>

</div>

<div class="products">

<?php foreach($section["products"] as $pi=>$p): ?>

<div class="product">

<div class="row g-2">

<div class="col-md-1">
<input class="form-control"
name="sections[<?=$si?>][products][<?=$pi?>][id]"
value="<?=$p["id"]?>">
</div>

<div class="col-md-3">
<input class="form-control"
name="sections[<?=$si?>][products][<?=$pi?>][name]"
value="<?=htmlspecialchars($p["name"])?>">
</div>

<div class="col-md-1">
<input class="form-control"
name="sections[<?=$si?>][products][<?=$pi?>][price]"
value="<?=$p["price"]?>">
</div>

<div class="col-md-1">
<input class="form-control"
name="sections[<?=$si?>][products][<?=$pi?>][oldPrice]"
value="<?=$p["oldPrice"]?>">
</div>

<div class="col-md-2">
<input class="form-control"
name="sections[<?=$si?>][products][<?=$pi?>][badge]"
value="<?=$p["badge"]?>">
</div>

<div class="col-md-3">
<input class="form-control img-input"
name="sections[<?=$si?>][products][<?=$pi?>][img]"
value="<?=$p["img"]?>">
</div>

<div class="col-md-1">
<img src="<?=$p["img"]?>" class="preview">
</div>

</div>

<div class="mt-2 d-flex justify-content-between">

<label>
<input type="checkbox"
name="sections[<?=$si?>][products][<?=$pi?>][soldOut]"
<?=isset($p["soldOut"])?"checked":""?>>
Esgotado
</label>

<button type="button"
class="btn btn-sm btn-danger remove-product">
<i class="bi bi-x"></i>
</button>

</div>

</div>

<?php endforeach; ?>

</div>

<button type="button"
class="btn btn-primary add-product mt-2">
<i class="bi bi-plus"></i> Adicionar Produto
</button>

</div>

<?php endforeach; ?>

</div>

<button type="button"
id="add-section"
class="btn btn-success mb-4">

<i class="bi bi-folder-plus"></i> Nova Categoria

</button>

<div class="text-center">

<button class="btn btn-lg btn-success">

<i class="bi bi-save"></i>
Salvar Alterações

</button>

</div>

</form>

</div>

<script>

function bindEvents(){

document.querySelectorAll(".remove-product").forEach(btn=>{
btn.onclick=()=>btn.closest(".product").remove()
})

document.querySelectorAll(".remove-section").forEach(btn=>{
btn.onclick=()=>{
if(confirm("Deseja remover esta categoria inteira?")){
btn.closest(".section").remove()
}
}
})

}

document.querySelectorAll(".add-product").forEach((btn,sectionIndex)=>{

btn.onclick=()=>{

let section=btn.closest(".section")
let container=section.querySelector(".products")
let productIndex=container.children.length

let html=`

<div class="product">

<div class="row g-2">

<div class="col-md-1">
<input class="form-control"
name="sections[${sectionIndex}][products][${productIndex}][id]"
placeholder="ID">
</div>

<div class="col-md-3">
<input class="form-control"
name="sections[${sectionIndex}][products][${productIndex}][name]"
placeholder="Nome">
</div>

<div class="col-md-1">
<input class="form-control"
name="sections[${sectionIndex}][products][${productIndex}][price]"
placeholder="Preço">
</div>

<div class="col-md-1">
<input class="form-control"
name="sections[${sectionIndex}][products][${productIndex}][oldPrice]"
placeholder="De">
</div>

<div class="col-md-2">
<input class="form-control"
name="sections[${sectionIndex}][products][${productIndex}][badge]"
placeholder="Badge">
</div>

<div class="col-md-3">
<input class="form-control img-input"
name="sections[${sectionIndex}][products][${productIndex}][img]"
placeholder="URL da imagem">
</div>

<div class="col-md-1">
<img class="preview">
</div>

</div>

<div class="mt-2 d-flex justify-content-between">

<label>
<input type="checkbox"
name="sections[${sectionIndex}][products][${productIndex}][soldOut]">
Esgotado
</label>

<button type="button"
class="btn btn-sm btn-danger remove-product">
<i class="bi bi-x"></i>
</button>

</div>

</div>
`

container.insertAdjacentHTML("beforeend",html)

bindEvents()

}

})

document.addEventListener("input",e=>{

if(e.target.classList.contains("img-input")){

let preview=e.target.closest(".row").querySelector(".preview")
preview.src=e.target.value

}

})

bindEvents()

</script>

</body>
</html>