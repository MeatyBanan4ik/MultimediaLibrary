--Создание таблиц
CREATE TABLE clients
(
    id bigserial PRIMARY KEY,
    email varchar NOT NULL UNIQUE,
    login varchar NOT NULL UNIQUE,
    password varchar NOT NULL,
    country varchar(60) NOT NULL,
    email_verified boolean DEFAULT('0') NOT NULL,
    hash varchar NOT NULL,
    rights varchar(10) DEFAULT('user') CHECK(rights IN('user', 'admin', 'creator')) NOT NULL,
    balance real CHECK(balance >= 0) DEFAULT(0.00) NOT NULL,
    avatar varchar(150) DEFAULT('default.jpg') NOT NULL,
    reg_d date DEFAULT(CURRENT_DATE) NOT NULL,
    last_t timestamp DEFAULT(CURRENT_TIMESTAMP) NOT NULL,
    rec_t timestamp DEFAULT(CURRENT_TIMESTAMP) NOT NULL
);

CREATE TABLE multimedia
(
    id bigserial PRIMARY KEY,
    price real NOT NULL CHECK(price >= 0 and price <= 10000),
    create_date date DEFAULT(CURRENT_DATE) NOT NULL,
    country varchar(60) NOT NULL,
    type varchar(10)  NOT NULL CHECK(type IN('audio', 'video', 'photo')),
    views integer DEFAULT(0) CHECK(views >= 0),
    name varchar(60) NOT NULL,
    description text  NOT NULL,
    count_orders integer DEFAULT(0) CHECK(count_orders >= 0) NOT NULL,
    count_comments integer DEFAULT(0) CHECK(count_comments >=0) NOT NULL,
    cover varchar DEFAULT('default.jpg') NOT NULL,
    avg_mark numeric DEFAULT(0) CHECK(avg_mark >= 0 and avg_mark <= 5) NOT NULL,
    id_client bigint NOT NULL
    REFERENCES clients(id) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL,
    id_administrator bigint
    REFERENCES clients(id)
    ON UPDATE CASCADE 
    ON DELETE SET NULL
);

CREATE TABLE mark
(
	PRIMARY KEY (id_multimedia, id_client),
    value integer NOT NULL CHECK(value >=1 AND value<=5),
    id_client bigint NOT NULL 
    REFERENCES clients(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    id_multimedia bigint NOT NULL
    REFERENCES multimedia(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE comments
(
    id bigserial PRIMARY KEY,
    text text NOT NULL,
    date timestamp DEFAULT(CURRENT_TIMESTAMP) NOT NULL,
    id_client bigint NOT NULL
    REFERENCES clients(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    id_multimedia bigint NOT NULL
    REFERENCES multimedia(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE orders
(
    id bigserial PRIMARY KEY,
    date timestamp DEFAULT(CURRENT_TIMESTAMP) NOT NULL,
    id_client bigint 
    REFERENCES clients(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
    state varchar CHECK(state IN('backet', 'paid')) NOT NULL
);

CREATE TABLE multimedia_list
(
	PRIMARY KEY (id_multimedia, id_order),
    id_order bigint NOT NULL 
    REFERENCES orders(id) 
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    id_multimedia bigint NOT NULL 
    REFERENCES multimedia(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE tags
(
    id bigserial PRIMARY KEY,
    name varchar(30) NOT NULL
);

CREATE TABLE tags_list
(
	PRIMARY KEY (id_multimedia, id_tag),
    id_multimedia bigint NOT NULL
    REFERENCES multimedia(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
    id_tag bigint NOT NULL
    REFERENCES tags(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
    
);

CREATE TABLE admins_log
(   
    login varchar,
    tablen varchar,
    role varchar,
    id bigint,  
    date timestamp DEFAULT(CURRENT_TIMESTAMP),
    action varchar
);
--Создание триггеров и триггерных функций
CREATE OR REPLACE FUNCTION admins_log_comments()
RETURNS TRIGGER
AS $$
BEGIN
    IF Tg_op ='INSERT' OR Tg_op ='UPDATE' THEN 
        SET TIMEZONE='Europe/Kiev';
        INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, NEW.id, CURRENT_TIMESTAMP, TG_OP);
        RETURN NEW;
    ELSE
        SET TIMEZONE='Europe/Kiev';
        INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, OLD.id, CURRENT_TIMESTAMP, TG_OP);
        RETURN OLD;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admins_log_multimedia()
RETURNS TRIGGER
AS $$
BEGIN
    IF Tg_op ='UPDATE' THEN
        IF OLD.views = NEW.views THEN
            SET TIMEZONE='Europe/Kiev';
            INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, NEW.id, CURRENT_TIMESTAMP, TG_OP);
        END IF;
        RETURN NEW;
    ELSEIF Tg_op ='INSERT' THEN
        SET TIMEZONE='Europe/Kiev';
        INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, NEW.id, CURRENT_TIMESTAMP, TG_OP);     
        RETURN NEW;
    ELSE
        SET TIMEZONE='Europe/Kiev';
        INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, OLD.id, CURRENT_TIMESTAMP, TG_OP);
        RETURN OLD;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admins_log_client()
RETURNS TRIGGER
AS $$
BEGIN
    IF Tg_op ='UPDATE' THEN 
        IF NEW.last_t = OLD.last_t THEN
            SET TIMEZONE='Europe/Kiev';
            INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, NEW.id, CURRENT_TIMESTAMP, TG_OP);
        END IF;
        RETURN NEW;
    ELSEIF Tg_op ='INSERT' THEN
        SET TIMEZONE='Europe/Kiev';
        INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, NEW.id, CURRENT_TIMESTAMP, TG_OP);     
        RETURN NEW;
    ELSE
        SET TIMEZONE='Europe/Kiev';
        INSERT INTO admins_log(login, tablen, role, id, date, action) VALUES (SESSION_USER, TG_TABLE_NAME, CURRENT_USER, OLD.id, CURRENT_TIMESTAMP, TG_OP);
        RETURN OLD;
    END IF;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER log
AFTER INSERT OR DELETE OR UPDATE ON clients
FOR EACH ROW EXECUTE PROCEDURE admins_log_client();

CREATE TRIGGER log
AFTER INSERT OR DELETE OR UPDATE ON multimedia
FOR EACH ROW EXECUTE PROCEDURE admins_log_multimedia();

CREATE TRIGGER log
AFTER INSERT OR DELETE OR UPDATE ON comments
FOR EACH ROW EXECUTE PROCEDURE admins_log_comments();

CREATE OR REPLACE FUNCTION count_comments()
RETURNS TRIGGER
AS $$
BEGIN
    IF (Tg_op ='INSERT' OR Tg_op ='UPDATE') THEN 
        UPDATE multimedia SET count_comments = (SELECT COUNT(id) FROM comments 
                                                WHERE id_multimedia = NEW.id_multimedia) WHERE id = NEW.id_multimedia;
        RETURN NEW;
    ELSE
        UPDATE multimedia SET count_comments = (SELECT COUNT(id) FROM comments 
                                                WHERE id_multimedia = OLD.id_multimedia) WHERE id = OLD.id_multimedia;
        RETURN OLD;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER countcomments
AFTER INSERT OR UPDATE OR DELETE ON comments
FOR EACH ROW EXECUTE PROCEDURE count_comments();

CREATE OR REPLACE FUNCTION avg_mark()
RETURNS TRIGGER
AS $$

BEGIN
    IF (Tg_op ='INSERT' OR Tg_op ='UPDATE') THEN 
        UPDATE multimedia SET avg_mark = (SELECT ROUND(AVG(value), 2) FROM mark 
                                          WHERE id_multimedia = NEW.id_multimedia) WHERE id = NEW.id_multimedia;
        RETURN NEW;
    ELSE
        UPDATE multimedia SET avg_mark = (SELECT ROUND(AVG(value), 2) FROM mark 
                                          WHERE id_multimedia = OLD.id_multimedia) WHERE id = OLD.id_multimedia;
        RETURN OLD;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER avgmark
AFTER INSERT OR UPDATE OR DELETE ON mark
FOR EACH ROW EXECUTE PROCEDURE avg_mark();

CREATE OR REPLACE FUNCTION count_orders()
RETURNS TRIGGER
AS $$
DECLARE
    i integer;
BEGIN
    IF (Tg_op ='INSERT' OR Tg_op ='UPDATE') THEN 
        FOR i IN (SELECT id_multimedia FROM multimedia_list WHERE id_order = NEW.id)
        LOOP
        UPDATE multimedia SET count_orders = (SELECT COUNT(orders.id) FROM multimedia_list 
                                              JOIN orders ON multimedia_list.id_order = orders.id 
                                              WHERE id_multimedia = i and orders.state = 'paid') WHERE id = i;
        END LOOP;
        RETURN NEW;
    ELSE
        FOR i IN (SELECT id_multimedia FROM multimedia_list WHERE id_order = OLD.id)
        LOOP
        UPDATE multimedia SET count_orders = (SELECT COUNT(orders.id) FROM multimedia_list 
                                              JOIN orders ON multimedia_list.id_order = orders.id 
                                              WHERE id_multimedia = i and orders.state = 'paid') WHERE id = i;
        END LOOP;
        RETURN OLD;     
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER countorders
AFTER INSERT OR UPDATE OR DELETE ON orders
FOR EACH ROW EXECUTE PROCEDURE count_orders();

CREATE OR REPLACE FUNCTION admin_check()
RETURNS TRIGGER
AS $$
BEGIN
    IF (SELECT rights FROM clients WHERE id=NEW.id_administrator) = 'user' THEN 
        RAISE EXCEPTION 'user не может одобрять мультимедиа';
    ELSEIF OLD.id_client = NEW.id_administrator THEN
        RAISE EXCEPTION 'Вы не можете одобрять свои же мультимедиа';
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER admincheck
AFTER UPDATE ON multimedia
FOR EACH ROW EXECUTE PROCEDURE admin_check();
--Хранимая процедура статистики по датам
CREATE OR REPLACE FUNCTION statistics(date1 date, date2 date, id_multimedia bigint)
RETURNS TABLE(count bigint, sum real)
AS $$
BEGIN
    IF date2 >= date1 THEN
        RETURN QUERY
        SELECT COUNT(multimedia_list.id_multimedia), SUM(multimedia.price) 
        FROM multimedia_list 
        JOIN orders 
        ON orders.id = multimedia_list.id_order
        JOIN multimedia
        ON multimedia.id = multimedia_list.id_multimedia
        WHERE orders.date >= statistics.date1::timestamp
        AND orders.date <= statistics.date2::timestamp
        AND orders.state = 'paid' 
        AND multimedia_list.id_multimedia = statistics.id_multimedia;
    ELSE 
        RAISE EXCEPTION 'date1 не может быть больше date2';
    END IF;
END;
$$ LANGUAGE plpgsql;
--Представления
CREATE VIEW w8_multimedia 
AS 
SELECT * FROM multimedia WHERE id_administrator IS NULL;
--Создание ролей
CREATE ROLE "user";
CREATE ROLE "admin";
CREATE ROLE "creator";
CREATE ROLE "non_auth";

GRANT SELECT ON TABLE multimedia TO "non_auth"; 
GRANT SELECT, INSERT, UPDATE ON TABLE multimedia TO "user"; 
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE multimedia TO "admin"; 
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE multimedia TO "creator"; 

GRANT SELECT, INSERT ON TABLE clients TO "non_auth"; 
GRANT SELECT, UPDATE ON TABLE clients TO "user"; 
GRANT SELECT, UPDATE ON TABLE clients TO "admin"; 
GRANT SELECT, UPDATE, DELETE ON TABLE clients TO "creator"; 

GRANT SELECT ON TABLE comments TO "non_auth"; 
GRANT SELECT, INSERT, DELETE ON TABLE comments TO "user"; 
GRANT SELECT, INSERT, DELETE ON TABLE comments TO "admin"; 
GRANT SELECT, INSERT, DELETE ON TABLE comments TO "creator"; 

GRANT SELECT ON TABLE orders TO "non_auth";
GRANT SELECT, INSERT, UPDATE ON TABLE orders TO "user"; 
GRANT SELECT, INSERT, UPDATE ON TABLE orders TO "admin"; 
GRANT SELECT, INSERT, UPDATE ON TABLE orders TO "creator"; 

GRANT SELECT ON TABLE multimedia_list TO "non_auth";
GRANT SELECT, INSERT, DELETE ON TABLE multimedia_list TO "user"; 
GRANT SELECT, INSERT, DELETE ON TABLE multimedia_list TO "admin"; 
GRANT SELECT, INSERT, DELETE ON TABLE multimedia_list TO "creator"; 

GRANT SELECT ON TABLE mark TO "non_auth";
GRANT SELECT, INSERT, UPDATE ON TABLE mark TO "user"; 
GRANT SELECT, INSERT, UPDATE ON TABLE mark TO "admin"; 
GRANT SELECT, INSERT, UPDATE ON TABLE mark TO "creator"; 

GRANT SELECT ON TABLE tags TO "non_auth";
GRANT SELECT, INSERT ON TABLE tags TO "user"; 
GRANT SELECT, INSERT ON TABLE tags TO "admin"; 
GRANT SELECT, INSERT ON TABLE tags TO "creator"; 

GRANT SELECT ON TABLE tags_list TO "non_auth";
GRANT SELECT, INSERT ON TABLE tags_list TO "user"; 
GRANT SELECT, INSERT ON TABLE tags_list TO "admin"; 
GRANT SELECT, INSERT ON TABLE tags_list TO "creator"; 

GRANT SELECT, INSERT ON TABLE admins_log TO "creator"; 
GRANT INSERT ON TABLE admins_log TO "non_auth"; 
GRANT INSERT ON TABLE admins_log TO "user"; 
GRANT INSERT ON TABLE admins_log TO "admin"; 

GRANT SELECT ON w8_multimedia TO "admin";
GRANT SELECT ON w8_multimedia TO "creator";

GRANT USAGE, SELECT ON SEQUENCE clients_id_seq TO "non_auth";

GRANT USAGE, SELECT ON SEQUENCE multimedia_id_seq TO "user";
GRANT USAGE, SELECT ON SEQUENCE multimedia_id_seq TO "admin";
GRANT USAGE, SELECT ON SEQUENCE multimedia_id_seq TO "creator";

GRANT USAGE, SELECT ON SEQUENCE tags_id_seq TO "user";
GRANT USAGE, SELECT ON SEQUENCE tags_id_seq TO "admin";
GRANT USAGE, SELECT ON SEQUENCE tags_id_seq TO "creator";

GRANT USAGE, SELECT ON SEQUENCE comments_id_seq TO "user";
GRANT USAGE, SELECT ON SEQUENCE comments_id_seq TO "admin";
GRANT USAGE, SELECT ON SEQUENCE comments_id_seq TO "creator";

GRANT USAGE, SELECT ON SEQUENCE orders_id_seq TO "user";
GRANT USAGE, SELECT ON SEQUENCE orders_id_seq TO "admin";
GRANT USAGE, SELECT ON SEQUENCE orders_id_seq TO "creator";

GRANT EXECUTE ON FUNCTION statistics(date1 date, date2 date, id_multimedia bigint) TO "user";
GRANT EXECUTE ON FUNCTION statistics(date1 date, date2 date, id_multimedia bigint) TO "admin";
GRANT EXECUTE ON FUNCTION statistics(date1 date, date2 date, id_multimedia bigint) TO "creator";

CREATE USER "non_authorized"	 WITH PASSWORD 'non_authorized';
GRANT "non_auth" TO "non_authorized";