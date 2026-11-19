--
-- PostgreSQL database dump
--

\restrict peyPwz0L6b34Gtv0Gp9lcA0ZCpEGnDcKVRSdPq5zf1DzamIXPexU5V5fmLU28cC

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: app1; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA app1;


--
-- Name: canteen_pos; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA canteen_pos;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: users; Type: TABLE; Schema: app1; Owner: -
--

CREATE TABLE app1.users (
    idno character varying(50) NOT NULL,
    username character varying(50) NOT NULL,
    pwd character varying(255) NOT NULL,
    name character varying(100) NOT NULL
);


--
-- Name: categories; Type: TABLE; Schema: canteen_pos; Owner: -
--

CREATE TABLE canteen_pos.categories (
    category_id integer NOT NULL,
    category_name character varying(100) NOT NULL
);


--
-- Name: categories_category_id_seq; Type: SEQUENCE; Schema: canteen_pos; Owner: -
--

CREATE SEQUENCE canteen_pos.categories_category_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categories_category_id_seq; Type: SEQUENCE OWNED BY; Schema: canteen_pos; Owner: -
--

ALTER SEQUENCE canteen_pos.categories_category_id_seq OWNED BY canteen_pos.categories.category_id;


--
-- Name: order_items; Type: TABLE; Schema: canteen_pos; Owner: -
--

CREATE TABLE canteen_pos.order_items (
    order_item_id integer NOT NULL,
    order_id integer NOT NULL,
    product_id integer NOT NULL,
    quantity integer NOT NULL,
    unit_price numeric(10,2) NOT NULL,
    line_total numeric(10,2) GENERATED ALWAYS AS (((quantity)::numeric * unit_price)) STORED,
    CONSTRAINT order_items_quantity_check CHECK ((quantity > 0)),
    CONSTRAINT order_items_unit_price_check CHECK ((unit_price >= (0)::numeric))
);


--
-- Name: order_items_order_item_id_seq; Type: SEQUENCE; Schema: canteen_pos; Owner: -
--

CREATE SEQUENCE canteen_pos.order_items_order_item_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: order_items_order_item_id_seq; Type: SEQUENCE OWNED BY; Schema: canteen_pos; Owner: -
--

ALTER SEQUENCE canteen_pos.order_items_order_item_id_seq OWNED BY canteen_pos.order_items.order_item_id;


--
-- Name: orders; Type: TABLE; Schema: canteen_pos; Owner: -
--

CREATE TABLE canteen_pos.orders (
    order_id integer NOT NULL,
    idno character varying(50) NOT NULL,
    customer_name character varying(100),
    order_date timestamp without time zone DEFAULT now() NOT NULL,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    total_amount numeric(10,2) DEFAULT 0.00 NOT NULL,
    CONSTRAINT orders_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'paid'::character varying, 'cancelled'::character varying])::text[])))
);


--
-- Name: orders_order_id_seq; Type: SEQUENCE; Schema: canteen_pos; Owner: -
--

CREATE SEQUENCE canteen_pos.orders_order_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: orders_order_id_seq; Type: SEQUENCE OWNED BY; Schema: canteen_pos; Owner: -
--

ALTER SEQUENCE canteen_pos.orders_order_id_seq OWNED BY canteen_pos.orders.order_id;


--
-- Name: payments; Type: TABLE; Schema: canteen_pos; Owner: -
--

CREATE TABLE canteen_pos.payments (
    payment_id integer NOT NULL,
    order_id integer NOT NULL,
    payment_method character varying(20) DEFAULT 'cash'::character varying NOT NULL,
    amount_paid numeric(10,2) NOT NULL,
    change_amount numeric(10,2) DEFAULT 0.00 NOT NULL,
    paid_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT payments_payment_method_check CHECK (((payment_method)::text = ANY ((ARRAY['cash'::character varying, 'gcash'::character varying])::text[])))
);


--
-- Name: payments_payment_id_seq; Type: SEQUENCE; Schema: canteen_pos; Owner: -
--

CREATE SEQUENCE canteen_pos.payments_payment_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: payments_payment_id_seq; Type: SEQUENCE OWNED BY; Schema: canteen_pos; Owner: -
--

ALTER SEQUENCE canteen_pos.payments_payment_id_seq OWNED BY canteen_pos.payments.payment_id;


--
-- Name: products; Type: TABLE; Schema: canteen_pos; Owner: -
--

CREATE TABLE canteen_pos.products (
    product_id integer NOT NULL,
    category_id integer,
    product_name character varying(150) NOT NULL,
    price numeric(10,2) DEFAULT 0.00 NOT NULL,
    stock_qty integer DEFAULT 0 NOT NULL,
    is_available boolean DEFAULT true NOT NULL
);


--
-- Name: products_product_id_seq; Type: SEQUENCE; Schema: canteen_pos; Owner: -
--

CREATE SEQUENCE canteen_pos.products_product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: products_product_id_seq; Type: SEQUENCE OWNED BY; Schema: canteen_pos; Owner: -
--

ALTER SEQUENCE canteen_pos.products_product_id_seq OWNED BY canteen_pos.products.product_id;


--
-- Name: categories category_id; Type: DEFAULT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.categories ALTER COLUMN category_id SET DEFAULT nextval('canteen_pos.categories_category_id_seq'::regclass);


--
-- Name: order_items order_item_id; Type: DEFAULT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.order_items ALTER COLUMN order_item_id SET DEFAULT nextval('canteen_pos.order_items_order_item_id_seq'::regclass);


--
-- Name: orders order_id; Type: DEFAULT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.orders ALTER COLUMN order_id SET DEFAULT nextval('canteen_pos.orders_order_id_seq'::regclass);


--
-- Name: payments payment_id; Type: DEFAULT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.payments ALTER COLUMN payment_id SET DEFAULT nextval('canteen_pos.payments_payment_id_seq'::regclass);


--
-- Name: products product_id; Type: DEFAULT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.products ALTER COLUMN product_id SET DEFAULT nextval('canteen_pos.products_product_id_seq'::regclass);


--
-- Data for Name: users; Type: TABLE DATA; Schema: app1; Owner: -
--

COPY app1.users (idno, username, pwd, name) FROM stdin;
1001	admin@school.com	$2y$12$wMpKH.Eahg.ODvg0dY90/uO3YdV63sse33air6IRCfTLXXR0SVKFa	Admin Cashier
1002	cashier1@school.com	$2y$12$v6SFH4zv17UU9OsPQzjUyu6rBTiZbNZwBzgQ7PnS9NTS8ofyQzW8y	Canteen Staff
1004	admin1	$2y$12$1mpMRum/LPP8oEmQgu5M8.WCWl2VtfihHtIymJa/WD6CIXZC/MUt6	Jolly Hope Dancila
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: canteen_pos; Owner: -
--

COPY canteen_pos.categories (category_id, category_name) FROM stdin;
1	Meals
2	Snacks
3	Beverages
4	Desserts
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: canteen_pos; Owner: -
--

COPY canteen_pos.order_items (order_item_id, order_id, product_id, quantity, unit_price) FROM stdin;
1	1	7	1	15.00
2	1	4	2	35.00
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: canteen_pos; Owner: -
--

COPY canteen_pos.orders (order_id, idno, customer_name, order_date, status, total_amount) FROM stdin;
1	1004	Walk-in Customer	2026-11-14 13:40:59	paid	85.00
\.


--
-- Data for Name: payments; Type: TABLE DATA; Schema: canteen_pos; Owner: -
--

COPY canteen_pos.payments (payment_id, order_id, payment_method, amount_paid, change_amount, paid_at) FROM stdin;
1	1	cash	85.00	0.00	2026-11-14 13:40:59
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: canteen_pos; Owner: -
--

COPY canteen_pos.products (product_id, category_id, product_name, price, stock_qty, is_available) FROM stdin;
1	1	Pork Sinigang with Rice	80.00	25	t
2	1	Chicken Adobo with Rice	75.00	30	t
3	1	Beef Caldereta with Rice	95.00	15	t
5	2	Crispy French Fries (Large)	45.00	20	t
6	3	House Blend Iced Tea (16 oz)	20.00	50	t
8	4	Special Halo-Halo	50.00	20	t
9	4	Creamy Leche Flan	30.00	15	t
7	3	Bottled Mineral Water (500ml)	15.00	59	t
4	2	Cheesy Lumpiang Shanghai (5 pcs)	35.00	38	t
\.


--
-- Name: categories_category_id_seq; Type: SEQUENCE SET; Schema: canteen_pos; Owner: -
--

SELECT pg_catalog.setval('canteen_pos.categories_category_id_seq', 4, true);


--
-- Name: order_items_order_item_id_seq; Type: SEQUENCE SET; Schema: canteen_pos; Owner: -
--

SELECT pg_catalog.setval('canteen_pos.order_items_order_item_id_seq', 2, true);


--
-- Name: orders_order_id_seq; Type: SEQUENCE SET; Schema: canteen_pos; Owner: -
--

SELECT pg_catalog.setval('canteen_pos.orders_order_id_seq', 1, true);


--
-- Name: payments_payment_id_seq; Type: SEQUENCE SET; Schema: canteen_pos; Owner: -
--

SELECT pg_catalog.setval('canteen_pos.payments_payment_id_seq', 1, true);


--
-- Name: products_product_id_seq; Type: SEQUENCE SET; Schema: canteen_pos; Owner: -
--

SELECT pg_catalog.setval('canteen_pos.products_product_id_seq', 9, true);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: app1; Owner: -
--

ALTER TABLE ONLY app1.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (idno);


--
-- Name: users users_username_key; Type: CONSTRAINT; Schema: app1; Owner: -
--

ALTER TABLE ONLY app1.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (category_id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (order_item_id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (order_id);


--
-- Name: payments payments_order_id_key; Type: CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.payments
    ADD CONSTRAINT payments_order_id_key UNIQUE (order_id);


--
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (payment_id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (product_id);


--
-- Name: idx_order_items_order; Type: INDEX; Schema: canteen_pos; Owner: -
--

CREATE INDEX idx_order_items_order ON canteen_pos.order_items USING btree (order_id);


--
-- Name: idx_orders_cashier; Type: INDEX; Schema: canteen_pos; Owner: -
--

CREATE INDEX idx_orders_cashier ON canteen_pos.orders USING btree (idno);


--
-- Name: idx_orders_date; Type: INDEX; Schema: canteen_pos; Owner: -
--

CREATE INDEX idx_orders_date ON canteen_pos.orders USING btree (order_date);


--
-- Name: idx_orders_status; Type: INDEX; Schema: canteen_pos; Owner: -
--

CREATE INDEX idx_orders_status ON canteen_pos.orders USING btree (status);


--
-- Name: idx_products_category; Type: INDEX; Schema: canteen_pos; Owner: -
--

CREATE INDEX idx_products_category ON canteen_pos.products USING btree (category_id);


--
-- Name: order_items order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.order_items
    ADD CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES canteen_pos.orders(order_id) ON DELETE CASCADE;


--
-- Name: order_items order_items_product_id_fkey; Type: FK CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.order_items
    ADD CONSTRAINT order_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES canteen_pos.products(product_id) ON DELETE RESTRICT;


--
-- Name: orders orders_idno_fkey; Type: FK CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.orders
    ADD CONSTRAINT orders_idno_fkey FOREIGN KEY (idno) REFERENCES app1.users(idno) ON DELETE RESTRICT;


--
-- Name: payments payments_order_id_fkey; Type: FK CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.payments
    ADD CONSTRAINT payments_order_id_fkey FOREIGN KEY (order_id) REFERENCES canteen_pos.orders(order_id) ON DELETE CASCADE;


--
-- Name: products products_category_id_fkey; Type: FK CONSTRAINT; Schema: canteen_pos; Owner: -
--

ALTER TABLE ONLY canteen_pos.products
    ADD CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES canteen_pos.categories(category_id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict peyPwz0L6b34Gtv0Gp9lcA0ZCpEGnDcKVRSdPq5zf1DzamIXPexU5V5fmLU28cC

