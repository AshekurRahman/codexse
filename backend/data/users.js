import bcrypt from "bcryptjs";
const users = [
  {
    name: "Admin",
    email: "admin@example.com",
    password: bcrypt.hashSync("123456", 12),
    isAdmin: true,
  },
  {
    name: "user",
    email: "user@example.com",
    password: bcrypt.hashSync("123456", 12),
  },
  {
    name: "John Doe",
    email: "john.doe@example.com",
    password: bcrypt.hashSync("password1", 12),
  },
  {
    name: "Jane Doe",
    email: "jane.doe@example.com",
    password: bcrypt.hashSync("password2", 12),
  },
  {
    name: "User1",
    email: "user1@example.com",
    password: bcrypt.hashSync("password3", 12),
  },
  {
    name: "User2",
    email: "user2@example.com",
    password: bcrypt.hashSync("password4", 12),
  },
];

export default users;
