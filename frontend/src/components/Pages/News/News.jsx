import React from "react";
import SiteHeader from "../../SiteHeader/SiteHeader";
import styles from "./News.module.css";
import NewsCard from "./NewsCard";

const News = () => {
  return (
    <div className={styles.news_page}>
      <SiteHeader title="News" />
      <div className="py-5">
        <div className="container">
          <div className="row">
            <div className="col-lg-4">
              <NewsCard />
            </div>
            <div className="col-lg-4">
              <NewsCard />
            </div>
            <div className="col-lg-4">
              <NewsCard />
            </div>
            <div className="col-lg-4">
              <NewsCard />
            </div>
            <div className="col-lg-4">
              <NewsCard />
            </div>
            <div className="col-lg-4">
              <NewsCard />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default News;
