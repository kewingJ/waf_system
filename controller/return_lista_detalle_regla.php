<?php
	require("../includes/config.php");
    require("../includes/security.php");

	$id_regla = $_POST['id_regla'];
    $consult = mysqli_query($link,"SELECT * FROM detalle_rule
                                WHERE id_rule = '$id_regla'");
    $i = 1;
    while($rows = mysqli_fetch_array($consult))
    {
        echo '
        <tr>
            <td>'.$i.'</td>
            <td>'.$rows['nombre_d_r'].'</td>
            <td>'.$rows['numero_rule_detalle'].'</td>
            <td>
                <a  class="btn btn-primary" href="#0"
                    data-toggle="modal"
                    data-target="#ModalEDetalleRegla"
                    data-id="'.$rows['id_detalle_r'].'"
                    data-nombre="'.$rows['nombre_d_r'].'"
                    data-codigo="'.$rows['numero_rule_detalle'].'"
                    id="btnEditarDetalleRegla">
                    <i class="fa fa-pencil"></i> Editar
                </a>
            </td>
        </tr>
        ';
        $i++;
    }
?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#exampleDetalle').DataTable();
    });
</script>
