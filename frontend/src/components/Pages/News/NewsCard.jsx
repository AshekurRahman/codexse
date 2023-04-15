import React from "react";
import { BiCommentDetail } from "react-icons/bi";
import { BsClock } from "react-icons/bs";
import { Link } from "react-router-dom";
import styles from "./News.module.css";

const NewsCard = () => {
  return (
    <div className={styles.post}>
      <Link to="/article" className={styles.post__img}>
        <img src="http://gogame.volkovdesign.com/img/posts/2.jpg" alt="" />
      </Link>
      <div className={styles.post__content}>
        <Link to="/" className={styles.post__category}>
          NFS
        </Link>
        <h3 className={styles.post__title}>
          <Link to="/">
            New hot race from your favorite computer games studio
          </Link>
        </h3>
        <div className={styles.post__meta}>
          <span className={styles.post__date}>
            <BsClock /> 2 hours ago
          </span>
          <span className={styles.post__comments}>
            <BiCommentDetail /> 18
          </span>
        </div>
      </div>
    </div>
  );
};

export default NewsCard;
