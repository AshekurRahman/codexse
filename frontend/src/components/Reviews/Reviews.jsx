import React from "react";

const Reviews = ({ reviews }) => {
  if (!reviews || !Array.isArray(reviews)) {
    return <div>No reviews available.</div>;
  }

  return (
    <div>
      <br />
      <br />
      <h3>Reviews</h3>
      <hr />
      {reviews.map((review) => (
        <div key={review._id}>
          <h4>{review.name}</h4>
          <p>{review.comment}</p>
          <div>Rating: {review.rating}</div>
        </div>
      ))}
    </div>
  );
};

export default Reviews;
