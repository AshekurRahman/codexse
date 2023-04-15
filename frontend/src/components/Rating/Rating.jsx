import { useState } from "react";
import { FaStar } from "react-icons/fa";
import styles from "./Rating.module.css";

const Rating = ({ product }) => {
  const [reviews] = useState(product.reviews);
  const [numReviews] = useState(product.numReviews);
  const [rating] = useState(product.rating);

  const getRatingPercentage = (starRating) => {
    return (starRating / numReviews) * 100;
  };
  return (
    <div className={styles.rating_progress}>
      <div className={styles.left}>
        <div className={styles.average}>
          <span className={styles.num}>{rating}</span>
          <FaStar />
        </div>
        <div className={styles.total_count}>{numReviews} Reviews</div>
      </div>
      <div className={styles.right}>
        <ul className={styles.rating_list}>
          {[5, 4, 3, 2, 1].map((star) => (
            <li key={star}>
              <span className={styles.label}>
                {star} <FaStar />
              </span>
              <div className={styles.progress}>
                <div
                  className={styles.bar}
                  style={{
                    width: `${getRatingPercentage(
                      reviews.filter((review) => review.rating === star).length
                    )}%`,
                  }}
                ></div>
              </div>
              <span className={styles.num}>
                {reviews.filter((review) => review.rating === star).length}
              </span>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default Rating;
