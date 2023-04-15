import bcrypt from "bcryptjs";
import mongoose from "mongoose";
import sanitizeHtml from "sanitize-html";

const reviewSchema = mongoose.Schema({
  product: {
    type: mongoose.Schema.Types.ObjectId,
    required: true,
    ref: "Product",
  },
  rating: { type: Number, required: true },
  comment: { type: String, required: true },
});

const userSchema = mongoose.Schema(
  {
    first_name: { type: String, required: true, minlength: 4 },
    last_name: { type: String, required: true, minlength: 4 },
    email: {
      type: String,
      required: true,
      unique: true,
      validate: {
        validator: (email) =>
          // Regex for email validation
          /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g.test(email),
        message: (props) => `${props.value} is not a valid email address!`,
      },
    },
    password: { type: String, required: true },
    privacyAccepted: { type: Boolean, required: true, default: false },
    isAdmin: { type: Boolean, required: true, default: false },
    reviews: [reviewSchema],
  },
  {
    timestamps: true,
  }
);

// Hash password before saving to the database
userSchema.pre("save", async function (next) {
  // Only hash the password if it is modified or new
  if (!this.isModified("password")) {
    next();
  }

  const salt = await bcrypt.genSalt(10);
  this.password = await bcrypt.hash(this.password, salt);
});

// Login
userSchema.methods.matchPassword = async function (enterPassword) {
  return await bcrypt.compare(enterPassword, this.password);
};

// Virtual property for the user's full name
userSchema.virtual("fullName").get(function () {
  return `${this.first_name} ${this.last_name}`;
});

// Sanitize user input before saving to the database
userSchema.pre("save", function (next) {
  this.first_name = sanitizeHtml(this.first_name);
  this.last_name = sanitizeHtml(this.last_name);
  next();
});

const User = mongoose.model("User", userSchema);

export default User;
