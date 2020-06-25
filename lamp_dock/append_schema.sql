--
-- テーブルの構造 `history`
--

CREATE TABLE history
(
    `order_id` INT
(11) NOT NULL,
    `user_id` INT
(11) NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルの構造 `details`
--
CREATE TABLE details
( 
    `order_id` INT
(11) NOT NULL,
    `item_id` INT
(11) NOT NULL,
    `price` INT
(11) NOT NULL,
    `amount` INT
(11) NOT NULL,
    `sum_price`INT
(11) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのインデックス `users`
--
ALTER TABLE `history`
ADD PRIMARY KEY
(`order_id`);


--
-- テーブルのAUTO_INCREMENT `history`
--
ALTER TABLE `history`
  MODIFY `order_id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

