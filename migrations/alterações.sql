CREATE TABLE adicional_categoria(
  id INT PRIMARY KEY AUTO_INCREMENT,
  adicional_id INT NOT NULL,
  categoria_id INT NOT NULL,
  FOREIGN KEY(adicional_id) REFERENCES adicional(id),
  FOREIGN KEY(categoria_id) REFERENCES categoria(id)
);
