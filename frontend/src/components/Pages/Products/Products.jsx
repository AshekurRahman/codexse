import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link } from "react-router-dom";
import { listProduct } from "../../../Redux/Actions/ProductActions";
import Message from "../../LoadinError/Error";
import Loading from "../../LoadinError/Loading";
import SiteHeader from "../../SiteHeader/SiteHeader";
import styles from "./Product.module.css";

const Products = () => {
  const dispatch = useDispatch();
  const productList = useSelector((state) => state.productList);
  const { loading, error, products } = productList;

  useEffect(() => {
    dispatch(listProduct());
  }, [dispatch]);

  return (
    <>
      <SiteHeader title={`Products`} />
      <div className="section-padding">
        <div className="container">
          <div className="row g-4">
            {loading ? (
              <div className="m-5">
                <Loading />
              </div>
            ) : error ? (
              <Message variant={`alert-danger`}>{error}</Message>
            ) : (
              products.map((product) => (
                <div key={product._id} className="col-md-4">
                  <div className={styles.product_box}>
                    <figure className={styles.thumb}>
                      <img src={product.image} alt={product.title} />
                    </figure>
                    <div className={styles.content}>
                      <h3 className={styles.title}>
                        <Link to={`/products/${product._id}`}>
                          {product.title}
                        </Link>
                      </h3>
                      <div className="d-flex justify-content-between">
                        {product.tags && (
                          <div className={styles.tags}>
                            <span>{product.tags[0]}</span>
                          </div>
                        )}
                        {product.price && (
                          <span className={styles.price}>${product.price}</span>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </>
  );
};

export default Products;
