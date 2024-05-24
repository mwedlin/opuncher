<!-- Show login box on front page -->
<div class="container-xl">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div class="card shadow-sm m-3">
        <div class="card-header bg-primary text-white"><?php echo $eventName;?></div>
        <div class="card-body">
          <form class="was-validated" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-row">
              <div class="col-12 col-md-9 mb-2 mb-md-0"><input class="form-control form-control-lg" type="text" name="ident" placeholder="!usercode!" /></div>
              <div class="col-12 col-md-3"><button class="btn btn-primary btn-lg" type="submit">!send!</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

