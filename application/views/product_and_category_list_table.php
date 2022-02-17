<div class="col-md-4 table-flip-scroll catg-tab" style="overflow: scroll;
    max-height: 100vh;">
    <table class="table category_tables" id="category_table">
      <?php 
            $this->load->view("category_list_table");
            //this is seprate for only table load by js by any action event
        ?>
    </table>
</div>
<div class="col-md-8 table-flip-scroll catg-tab">
    <table class="table category_tables" id="product_table">
        <?php 
              $this->load->view("product_list_table");
              //this is seprate for only table load by js by any action event
          ?>
    </table>
    <!--  <nav class="text-xs-right">
        <?php// if (isset($links)) { ?>
            <?php //echo $links; ?>
        <?php //} ?>
    </nav> -->
</div>