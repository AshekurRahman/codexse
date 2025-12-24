import React, { useEffect, useState } from "react";
import { BsCloudDownload } from "react-icons/bs";
import {
  SlBasketLoaded,
  SlCalender,
  SlPin,
  SlRefresh,
  SlTag,
} from "react-icons/sl";
import { useDispatch, useSelector } from "react-redux";
import { Link, useParams } from "react-router-dom"; // Modified import statement
import { listProductDetails } from "../../../Redux/Actions/ProductActions";
import Button from "../../Button/Button";
import Message from "../../LoadinError/Error";
import Loading from "../../LoadinError/Loading";
import Rating from "../../Rating/Rating";
import ReviewForm from "../../ReviewForm/ReviewForm";
import Reviews from "../../Reviews/Reviews";
import Share from "../../Share/Share";
import styles from "./Product.module.css";

const SingleProduct = ({ match }) => {
  // 👇️ get ID from url
  const { id: productId } = useParams();
  const dispatch = useDispatch();
  const productDetails = useSelector((state) => state.productDetails);
  const { loading, error, product } = productDetails;
  const [productUrl, setProductUrl] = useState("");

  useEffect(() => {
    dispatch(listProductDetails(productId));
    setProductUrl(window.location.href);
  }, [dispatch, productId]);

  const addToCart = (e) => {
    e.preventDefault();
  };

  return (
    <>
      <div className="section-padding">
        <div
          className={styles.single_bg}
          style={{ backgroundImage: `url(${product.image})` }}
        ></div>
        <div className="container">
          {loading ? (
            <Loading />
          ) : error ? (
            <Message variant={`alert-danger`}>{error}</Message>
          ) : (
            <>
              <div className="row g-0">
                <div className="col-sm-12 mb-5">
                  <h2 className="page_title">{product.title}</h2>
                  {product.description && <p>{product.description}</p>}
                </div>
                <div className="col-sm-6">
                  <ul className={styles.info_list}>
                    {product.updatedAt && (
                      <li>
                        <span className={styles.icon}>
                          <SlRefresh />
                        </span>
                        <span className={styles.label}>Last update:</span>
                        <span className={styles.text}>
                          {new Date(product.updatedAt).toLocaleString("en-US", {
                            month: "short",
                            day: "numeric",
                            year: "numeric",
                          })}
                        </span>
                      </li>
                    )}
                    {product.createdAt && (
                      <li>
                        <span className={styles.icon}>
                          <SlCalender />
                        </span>
                        <span className={styles.label}>Published:</span>
                        <span className={styles.text}>
                          {new Date(product.createdAt).toLocaleString("en-US", {
                            month: "short",
                            day: "numeric",
                            year: "numeric",
                          })}
                        </span>
                      </li>
                    )}
                    {product.layout && (
                      <li>
                        <span className={styles.icon}>
                          <SlPin />
                        </span>
                        <span className={styles.label}>Layout:</span>
                        <span className={styles.text}>{product.layout}</span>
                      </li>
                    )}
                    {product.tags && (
                      <li>
                        <span className={styles.icon}>
                          <SlTag />
                        </span>
                        <span className={styles.label}>Tags:</span>
                        <span className={styles.text}>
                          {product.tags?.map((tag) => (
                            <Link
                              to={`/tags/${tag
                                .replace(/ /g, "-")
                                .toLowerCase()}`}
                            >
                              {tag}
                            </Link>
                          ))}
                        </span>
                      </li>
                    )}
                  </ul>
                  <div className="pb-2"></div>
                  <hr />
                  <ul className={styles.info_list}>
                    <li>
                      <span className={styles.icon}>
                        <SlBasketLoaded />
                      </span>
                      <span className={styles.text}>166 Sales</span>
                    </li>

                    <li>
                      <span className={styles.icon}>
                        <BsCloudDownload />
                      </span>
                      <span className={styles.text}>96 Downloads</span>
                    </li>
                  </ul>
                </div>
                <div className="col-sm-6">
                  <Button onClick={addToCart}>
                    Add to cart - ${product?.price}
                  </Button>
                  <div className={styles.product_actions}>
                    <a
                      href={product.livePreview}
                      target="_blank"
                      className="outline_button"
                      rel="noreferrer"
                    >
                      Live Preview
                    </a>
                    <a
                      href={product.demoUrl}
                      target="_blank"
                      className="outline_button"
                      rel="noreferrer"
                    >
                      Demo Link
                    </a>
                  </div>
                </div>
              </div>
              <div className="row g-5 mt-0">
                {product.gallery?.map((value) => (
                  <div className="col-lg-6">
                    <figure className="m-0">
                      <img src={value} alt="" className="w-100" />
                    </figure>
                  </div>
                ))}
              </div>
              <Reviews reviews={product.reviews} />
              <div className="row g-5 mt-2">
                <div className="col-lg-6">
                  <ReviewForm />
                </div>
                <div className="col-lg-6">
                  <Rating product={product} />
                  <hr />
                  <Share productUrl={productUrl} />
                </div>
              </div>
            </>
          )}
        </div>
      </div>
    </>
  );
};

export default SingleProduct;
