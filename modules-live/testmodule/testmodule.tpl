<div class="container-fluid">
  <header class="row">
    <div class="header-top clearfix">
      <div class="header-container">
        <div class="pull-left">
          <div class="btn-group language-switcher">
            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{$urls.img_url}/bee-flag-fr.svg" alt="" /> <span>Fr</span> <i class="fa fa-angle-down"></i>
                            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-flag-fr.svg" alt=""/> <span>FR</span></a></li>
              <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-flag-en.svg" alt=""/> <span>EN</span></a></li>
            </ul>
          </div>
        </div>
        <div class="pull-right">
          <div class="header-top-nav clearfix">
            <ul class="clearfix">
              <li><a href="javascript:void(0);" class="wishlist-list-hover"><img src="{$urls.img_url}/bee-wishlist-g3.svg" alt="" /><span> Wishlist</span></a></li>
              <li><a href="javascript:void(0);" class="user-list-hover"><img src="{$urls.img_url}/bee-moncompte-g2.svg" alt="" /><span> Créer un compte</span></a></li>
              <li><a href="javascript:void(0);" class="login-list-hover"><img src="{$urls.img_url}/bee-login-g2.svg" alt="" /><span> Se connecter</span></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="middle-nav clearfix">
      <div class="header-container">
        <div class="pull-left">
          <a href="index.html" class="header-logo"><img src="{$urls.img_url}/bee-logo-g4.png" alt="" /></a>
        </div>
        <div class="pull-right">
          <ul class="header-middle-nav clearfix">
            <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-illu-panier.png" alt="" /></a></li>
            <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-illu-sacados-bretelles.png" alt="" /></a></li>
            <li class="hide-mobile-activity">
              <a href="javascript:void(0);">
                <div class="btn btn-yellow">Proposez une activité</div>
              </a>
            </li>
            <li class="mobile-activity"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-activites-g4.svg" alt="" /></a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="header-navigation clearfix">
      <div class="header-container">
        <div class="pull-left">
          <div class="navbar navbar-default">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed navbtn-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
					            </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="main-navigation clearfix">
                <li><a href="javascript:void(0);">Qui sommes nous ?</a></li>
                <li><a href="javascript:void(0);">La place du marché</a></li>
                <li><a href="javascript:void(0);">Trouvez une activité</a></li>
                <li><a href="javascript:void(0);">Quoi de neuf ?</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="pull-right">
          <div class="search-wrap">
            <input type="text" class="form-control" name="txt-search" placeholder="Rechercher sur le site" />
            <div class="search-icon-wrap"><a href="javascript:void(0)"><img src="{$urls.img_url}/bee-search-g3.svg" alt="" /></a></div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!--header -->
  <div class="row marketplace-wrap">
    <div class="container">
      <div class="col-md-9">
        <div class="owl-carousel">
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-1.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Jean-Christophe, Apiculteur</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>50.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-1-->
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-2.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Prénom, Métier</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>210.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-2-->
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-3.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Prénom, Métier</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>20.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-3-->
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-4.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Prénom, Métier</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>20.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-4-->
        </div>
      </div>
    </div>
  </div>
  <!--row marketplace-wrap-->

  <div class="row discover-categories">
    <div class="container">
      <div class="col-sm-12 section-heading">
        <h2>Découvrez nos catégories</h2></div>
      <div class="col-sm-12">
        <div class="owl-carousel-wrapper">
          <div class="owl-carousel">
            <div>
              <div class="category-wrapper">
                <a href="javascript:void(0);">
                  <div class="category-img">
                    <img src="{$urls.img_url}/category-img.jpg" alt="" />
                  </div>
                  <h3>Catégorie 01</h3>
                </a>
              </div>
            </div>
            <div>
              <div class="category-wrapper">
                <a href="javascript:void(0);">
                  <div class="category-img">
                    <img src="{$urls.img_url}/category-img.jpg" alt="" />
                  </div>
                  <h3>Catégorie 02</h3>
                </a>
              </div>
            </div>
            <div>
              <div class="category-wrapper">
                <a href="javascript:void(0);">
                  <div class="category-img">
                    <img src="{$urls.img_url}/category-img.jpg" alt="" />
                  </div>
                  <h3>Catégorie 03</h3>
                </a>
              </div>
            </div>
            <div>
              <div class="category-wrapper">
                <a href="javascript:void(0);">
                  <div class="category-img">
                    <img src="{$urls.img_url}/category-img.jpg" alt="" />
                  </div>
                  <h3>Catégorie 04</h3>
                </a>
              </div>
            </div>
            <div>
              <div class="category-wrapper">
                <a href="javascript:void(0);">
                  <div class="category-img">
                    <img src="{$urls.img_url}/category-img.jpg" alt="" />
                  </div>
                  <h3>Catégorie 05</h3>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--row discover-categories-->
  <div class="row artist-month-wrap">
    <div class="container">
      <div class="col-sm-12 section-heading">
        <h2>Les artisans du mois</h2></div>
      <div class="col-sm-12">
        <div class="owl-carousel">
          <div>
            <div class="artist-month-outer clearfix">
              <div class="artist-month-det">
                <div class="user-detail">
                  <a href="javascript:void(0);">
                    <div class="media">
                      <div class="media-left">
                        <div class="user-image">
                          <div class="overflow-hidden"><img class="user-pic" src="{$urls.img_url}/user-small.jpg" alt=""></div>
                          <img class="shop-icon-img" src="{$urls.img_url}/bee-shop-petit_label.svg" alt="">
                        </div>
                      </div>
                      <div class="media-body">
                        <p>Prénom Métier</p>
                        <p>Ville <br />Pays (00)</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="artist-quote">
                  <img src="{$urls.img_url}/bee-quote-g4.svg" alt="" />
                  <p> Ximos sitatur aut ut exeribus aut eat optaqua tempore quat. Digendae voluptur alitis di vo-luptatibus et fugit voloris debis volupta !</p>
                </div>
                <div>
                  <a href="javascript:void(0);" class="btn btn-shop-link"> <img src="{$urls.img_url}/bee-shop-g4.svg" alt="" /> Visitez la boutique</a>
                  <a href="javascript:void(0)" class="btn btn-yellow btn-block"> <img src="{$urls.img_url}/bee-rencontrez-bl.svg" alt="" />  Rencontrez l’artisan</a>
                </div>
              </div>
              <div class="artist-month-shop">
                <img src="{$urls.img_url}/artist-month-1.jpg" alt="" />
              </div>
            </div>
            <!--artist-month-outer clearfix-->
          </div>
          <div>
            <div class="artist-month-outer clearfix">
              <div class="artist-month-det">
                <div class="user-detail">
                  <a href="javascript:void(0);">
                    <div class="media">
                      <div class="media-left">
                        <div class="user-image">
                          <div class="overflow-hidden"><img class="user-pic" src="{$urls.img_url}/user-small.jpg" alt=""></div>
                          <img class="shop-icon-img" src="{$urls.img_url}/bee-shop-petit_label.svg" alt="">
                        </div>
                      </div>
                      <div class="media-body">
                        <p>Prénom Métier</p>
                        <p>Ville <br />Pays (00)</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="artist-quote">
                  <img src="{$urls.img_url}/bee-quote-g4.svg" alt="" />
                  <p> Ximos sitatur aut ut exeribus aut eat optaqua tempore quat. Digendae voluptur alitis di vo-luptatibus et fugit voloris debis volupta !</p>
                </div>
                <div>
                  <a href="javascript:void(0);" class="btn btn-shop-link"> <img src="{$urls.img_url}/bee-shop-g4.svg" alt="" /> Visitez la boutique</a>
                  <a href="javascript:void(0)" class="btn btn-yellow btn-block"> <img src="{$urls.img_url}/bee-rencontrez-bl.svg" alt="" />  Rencontrez l’artisan</a>
                </div>
              </div>
              <div class="artist-month-shop">
                <img src="{$urls.img_url}/artist-month-2.jpg" alt="" />
              </div>
            </div>
            <!--artist-month-outer clearfix-->
          </div>
          <div>
            <div class="artist-month-outer clearfix">
              <div class="artist-month-det">
                <div class="user-detail">
                  <a href="javascript:void(0);">
                    <div class="media">
                      <div class="media-left">
                        <div class="user-image">
                          <div class="overflow-hidden"><img class="user-pic" src="{$urls.img_url}/user-small.jpg" alt=""></div>
                          <img class="shop-icon-img" src="{$urls.img_url}/bee-shop-petit_label.svg" alt="">
                        </div>
                      </div>
                      <div class="media-body">
                        <p>Prénom Métier</p>
                        <p>Ville <br />Pays (00)</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="artist-quote">
                  <img src="{$urls.img_url}/bee-quote-g4.svg" alt="" />
                  <p> Ximos sitatur aut ut exeribus aut eat optaqua tempore quat. Digendae voluptur alitis di vo-luptatibus et fugit voloris debis volupta !</p>
                </div>
                <div>
                  <a href="javascript:void(0);" class="btn btn-shop-link"> <img src="{$urls.img_url}/bee-shop-g4.svg" alt="" /> Visitez la boutique</a>
                  <a href="javascript:void(0)" class="btn btn-yellow btn-block"> <img src="{$urls.img_url}/bee-rencontrez-bl.svg" alt="" />  Rencontrez l’artisan</a>
                </div>
              </div>
              <div class="artist-month-shop">
                <img src="{$urls.img_url}/artist-month-1.jpg" alt="" />
              </div>
            </div>
            <!--artist-month-outer clearfix-->
          </div>
        </div>
        <!--owl-carousel-->
      </div>
    </div>
  </div>
  <!--row artist-month-wrap-->
  <div class="row favorites-week">
    <div class="container">
      <div class="col-sm-12 section-heading">
        <h2>Les coups de cœur de la semaine</h2></div>
      <div class="col-sm-12">
        <div class="owl-carousel">
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-1.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Jean-Christophe, Apiculteur</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>50.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-1-->
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-2.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Prénom, Métier</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>210.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-2-->
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-3.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Prénom, Métier</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>20.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-3-->
          <div>
            <div class="product-wrap">
              <div class="user-image">
                <img class="main-user-image" src="{$urls.img_url}/user-small.jpg" alt="" />
                <img class="hover-img" src="{$urls.img_url}/bee-shop-hover-artisan.svg" alt="" />
              </div>
              <div class="product-image">
                <a href="javascript:void(0);">
                                        <img src="{$urls.img_url}/product-4.jpg" alt=""/>
                                    </a>
                <div class="hover-btnlist">
                  <ul>
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-apercu-g4.svg" alt="" /></a></li>
                    {* <li class="active"><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-favori-g4.svg" alt="" /></a></li> *}
                    <li><a href="javascript:void(0);"><img src="{$urls.img_url}/bee-cart-g4.svg" alt="" /></a></li>
                  </ul>
                </div>
              </div>
              <div class="product-name-price clearfix">
                <p>Prénom, Métier</p>
                <div class="pull-left">
                  <p>Nom du produit coupé...</p>
                </div>
                <div class="pull-right price">
                  <p>20.00 €</p>
                </div>
              </div>
            </div>
          </div>
          <!--product-4-->
        </div>
      </div>
    </div>
  </div>
