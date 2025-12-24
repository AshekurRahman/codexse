import React, { useState } from "react";
import { FaStar } from "react-icons/fa";
import styles from "./ReviewForm.module.css";

const ReviewForm = () => {
  const [name, setName] = useState("");
  const [rating, setRating] = useState(0);
  const [comment, setComment] = useState("");

  const handleNameChange = (event) => {
    setName(event.target.value);
  };

  const handleRatingChange = (event) => {
    setRating(event.target.value);
  };

  const handleCommentChange = (event) => {
    setComment(event.target.value);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    // TODO: Submit the review data to the server
    console.log({ name, rating, comment });

    // Reset the form
    setName("");
    setRating(0);
    setComment("");
  };

  const Star = ({ filled }) => (
    <FaStar color={filled ? "#ffc107" : "#e4e5e9"} />
  );

  return (
    <div>
      <h3>Add a Review</h3>
      <form className={styles.review_form} onSubmit={handleSubmit}>
        <div>
          <label htmlFor="name">Name:</label>
          <input
            type="text"
            id="name"
            value={name}
            onChange={handleNameChange}
            required
          />
        </div>
        <div>
          <label htmlFor="rating">Rating:</label>
          <div>
            {[...Array(5)].map((_, index) => {
              const filled = index + 1 <= rating;
              return (
                <label key={index}>
                  <input
                    type="radio"
                    name="rating"
                    value={index + 1}
                    checked={filled}
                    onChange={handleRatingChange}
                  />
                  <Star filled={filled} />
                </label>
              );
            })}
          </div>
        </div>
        <div>
          <label htmlFor="comment">Comment:</label>
          <textarea
            id="comment"
            value={comment}
            onChange={handleCommentChange}
            required
          />
        </div>
        <button type="submit">Submit</button>
      </form>
    </div>
  );
};

export default ReviewForm;
