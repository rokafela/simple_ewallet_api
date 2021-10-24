CREATE TABLE public.t_mtr_user (
	id serial8 PRIMARY KEY,
	username varchar UNIQUE NOT NULL,
    balance int4 NOT NULL DEFAULT 0,
	create_date date NOT NULL DEFAULT CURRENT_DATE,
    token text
);

-- initial user to ensure test cases work correctly
INSERT INTO public.t_mtr_user (username,balance,create_date,"token") VALUES
	('foo',0,CURRENT_DATE,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c3IiOiJmb28ifQ.2_hCr4r5gvV3lkEXz8vpOVYI5oSO7Qwa_R4IqXQj1Zw');

CREATE TABLE public.t_trx_topup (
    id serial8 PRIMARY KEY,
    transaction_type varchar NOT NULL,
    amount int4 NOT NULL,
    username varchar NOT NULL,
    before_balance int4 NOT NULL,
    after_balance int4 NOT NULL,
    transaction_time timestamptz NOT NULL
);

CREATE TABLE public.t_trx_transfer (
    id serial8 PRIMARY KEY,
    transaction_type varchar NOT NULL,
    amount int4 NOT NULL,
    sender_username varchar NOT NULL,
    sender_before_balance int4 NOT NULL,
    sender_after_balance int4 NOT NULL,
    receiver_username varchar NOT NULL,
    receiver_before_balance int4 NOT NULL,
    receiver_after_balance int4 NOT NULL,
    transaction_time timestamptz NOT NULL
);