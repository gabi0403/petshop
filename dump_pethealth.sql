--
-- PostgreSQL database dump
--

\restrict O0twAAOhWch8mLlt0HrsXigNATc80jS83RNSoTgMca42QhaEfn2fJa75Bjgc6lL

-- Dumped from database version 18.4
-- Dumped by pg_dump version 18.4

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: agendamentos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.agendamentos (
    id integer NOT NULL,
    pet_id integer,
    servico_id integer,
    funcionario_id integer,
    data_hora timestamp without time zone NOT NULL,
    status character varying(30) DEFAULT 'Agendado'::character varying NOT NULL,
    CONSTRAINT agendamentos_status_check CHECK (((status)::text = ANY ((ARRAY['Agendado'::character varying, 'Em Atendimento'::character varying, 'Pronto para Retirada'::character varying, 'Finalizado'::character varying])::text[])))
);


ALTER TABLE public.agendamentos OWNER TO postgres;

--
-- Name: agendamentos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.agendamentos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.agendamentos_id_seq OWNER TO postgres;

--
-- Name: agendamentos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.agendamentos_id_seq OWNED BY public.agendamentos.id;


--
-- Name: log_atividades; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.log_atividades (
    id integer NOT NULL,
    usuario_id integer,
    acao text NOT NULL,
    data_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.log_atividades OWNER TO postgres;

--
-- Name: log_atividades_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.log_atividades_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.log_atividades_id_seq OWNER TO postgres;

--
-- Name: log_atividades_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.log_atividades_id_seq OWNED BY public.log_atividades.id;


--
-- Name: mural_avisos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mural_avisos (
    id integer NOT NULL,
    usuario_id integer,
    titulo character varying(100) NOT NULL,
    conteudo text NOT NULL,
    data_publicacao timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    urgencia character varying(10) DEFAULT 'baixa'::character varying
);


ALTER TABLE public.mural_avisos OWNER TO postgres;

--
-- Name: mural_avisos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mural_avisos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mural_avisos_id_seq OWNER TO postgres;

--
-- Name: mural_avisos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mural_avisos_id_seq OWNED BY public.mural_avisos.id;


--
-- Name: pets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pets (
    id integer NOT NULL,
    cliente_id integer,
    nome character varying(50) NOT NULL,
    especie character varying(50) NOT NULL,
    raca character varying(50) DEFAULT 'SRD'::character varying,
    data_nascimento date,
    comportamento character varying(50) DEFAULT 'Normal'::character varying,
    foto character varying(255) DEFAULT 'default_pet.png'::character varying
);


ALTER TABLE public.pets OWNER TO postgres;

--
-- Name: pets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pets_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pets_id_seq OWNER TO postgres;

--
-- Name: pets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pets_id_seq OWNED BY public.pets.id;


--
-- Name: servicos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.servicos (
    id integer NOT NULL,
    nome_servico character varying(100) NOT NULL,
    categoria character varying(20) NOT NULL,
    preco numeric(10,2) NOT NULL,
    CONSTRAINT servicos_categoria_check CHECK (((categoria)::text = ANY ((ARRAY['estetica'::character varying, 'clinica'::character varying])::text[])))
);


ALTER TABLE public.servicos OWNER TO postgres;

--
-- Name: servicos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.servicos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.servicos_id_seq OWNER TO postgres;

--
-- Name: servicos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.servicos_id_seq OWNED BY public.servicos.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    senha character varying(255) NOT NULL,
    telefone character varying(20),
    tipo character varying(20) DEFAULT 'cliente'::character varying NOT NULL,
    cargo character varying(30) DEFAULT 'Cliente'::character varying,
    criado_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT usuarios_cargo_final_check CHECK (((cargo)::text = ANY ((ARRAY['Veterinário'::character varying, 'Tosador'::character varying, 'Atendente'::character varying, 'Recepcionista'::character varying, 'Gerente'::character varying, 'Cliente'::character varying])::text[]))),
    CONSTRAINT usuarios_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['equipe'::character varying, 'cliente'::character varying])::text[])))
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: agendamentos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agendamentos ALTER COLUMN id SET DEFAULT nextval('public.agendamentos_id_seq'::regclass);


--
-- Name: log_atividades id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_atividades ALTER COLUMN id SET DEFAULT nextval('public.log_atividades_id_seq'::regclass);


--
-- Name: mural_avisos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mural_avisos ALTER COLUMN id SET DEFAULT nextval('public.mural_avisos_id_seq'::regclass);


--
-- Name: pets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pets ALTER COLUMN id SET DEFAULT nextval('public.pets_id_seq'::regclass);


--
-- Name: servicos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.servicos ALTER COLUMN id SET DEFAULT nextval('public.servicos_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Data for Name: agendamentos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.agendamentos (id, pet_id, servico_id, funcionario_id, data_hora, status) FROM stdin;
25	14	7	11	2026-06-18 16:40:00	Agendado
26	11	1	11	2026-06-09 16:44:00	Em Atendimento
27	7	2	22	2026-06-10 16:51:00	Pronto para Retirada
\.


--
-- Data for Name: log_atividades; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.log_atividades (id, usuario_id, acao, data_hora) FROM stdin;
1	\N	Camila iniciou o atendimento de Banho Geral Completo para o pet Thor.	2026-05-28 11:17:16.577298
2	\N	Camila iniciou o atendimento de Banho Geral Completo para o pet Thor.	2026-05-28 11:30:44.350389
3	\N	Camila iniciou o atendimento de Banho Geral Completo para o pet Thor.	2026-05-28 11:30:46.50581
4	\N	Camila iniciou o atendimento de Banho Geral Completo para o pet Thor.	2026-05-28 11:30:48.121585
5	12	O usuário Carlos Tutor (Cliente) realizou login no sistema.	2026-06-02 16:52:32.880162
6	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-02 16:53:00.015913
7	11	Cadastrou o pet 'cobra pipoca' (Cobra) para o tutor de ID 12.	2026-06-02 16:56:21.495568
8	11	Cadastrou o pet 'pipoca' (Cobra) para o tutor de ID 12.	2026-06-02 16:57:32.228653
9	11	Cadastrou o pet 'Panda' (Urso) para o tutor de ID 12.	2026-06-02 16:59:18.222351
10	11	Cadastrou o novo cliente tutor: 'Evelyn' (Email: evelyn@gmail.com).	2026-06-02 17:01:20.53098
11	11	Cadastrou o pet 'Bolt' (Cão) para o tutor de ID 13.	2026-06-02 17:02:59.819493
12	11	Cadastrou o pet 'Bolt' (Cão) para o tutor de ID 13.	2026-06-02 17:04:11.122445
13	11	Inseriu um novo agendamento na esteira para o pet ID 7 em 01/07/17:05	2026-06-02 17:05:19.395873
14	11	Camila Silva Tosadora (Tosador) moveu o status do atendimento #5 para 'Agendado'.	2026-06-02 17:05:23.573537
15	11	Camila Silva Tosadora (Tosador) moveu o status do atendimento #5 para 'Agendado'.	2026-06-02 17:05:25.166238
16	11	Inseriu um novo agendamento na esteira para o pet ID 4 em 05/06/17:05	2026-06-02 17:05:43.010311
17	11	Camila Silva Tosadora (Tosador) moveu o status do atendimento #6 para 'Finalizado'.	2026-06-02 17:06:26.28517
18	13	O usuário Evelyn (Cliente) realizou login no sistema.	2026-06-02 17:07:31.319458
19	10	O usuário Dr. Roberto Garcia (Veterinário) realizou login no sistema.	2026-06-02 17:08:16.444782
20	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #5 para 'Em Atendimento'.	2026-06-02 17:08:35.521846
21	10	Cadastrou o novo cliente tutor: 'Jorge' (Email: jorge@gmail.com).	2026-06-02 17:09:21.632637
22	10	Removeu o cliente de ID #14 do sistema.	2026-06-02 17:09:38.868656
23	10	Cadastrou o novo cliente tutor: 'Jorge' (Email: jorge@gmail.com).	2026-06-02 17:10:03.203148
24	10	Cadastrou o pet 'Nina' (Cão) para o tutor de ID 15.	2026-06-02 17:10:40.659985
25	10	Inseriu um novo agendamento na esteira para o pet ID 8 em 05/06/17:10	2026-06-02 17:10:56.938347
26	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #7 para 'Finalizado'.	2026-06-02 17:11:06.966124
27	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-09 13:21:52.127855
29	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-09 13:34:06.086667
30	12	O usuário Carlos Tutor (Cliente) realizou login no sistema.	2026-06-09 13:37:15.487676
31	10	O usuário Dr. Roberto Garcia (Veterinário) realizou login no sistema.	2026-06-09 13:39:20.02181
32	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #7 para 'Em Atendimento'.	2026-06-09 13:41:29.171941
33	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Pronto para Retirada'.	2026-06-09 13:41:31.610388
34	10	Inseriu um novo agendamento na esteira para o pet ID 7 em 10/06/13:41	2026-06-09 13:41:51.832368
35	10	Inseriu um novo agendamento na esteira para o pet ID 7 em 10/06/13:41	2026-06-09 13:42:15.772056
36	10	Inseriu um novo agendamento na esteira para o pet ID 7 em 10/06/13:41	2026-06-09 13:42:58.278426
37	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #8 para 'Pronto para Retirada'.	2026-06-09 13:44:40.869233
38	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #10 para 'Agendado'.	2026-06-09 13:44:42.013968
39	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #9 para 'Agendado'.	2026-06-09 13:44:42.956529
40	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #7 para 'Em Atendimento'.	2026-06-09 13:44:45.740695
41	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Pronto para Retirada'.	2026-06-09 13:44:46.781921
42	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #9 para 'Finalizado'.	2026-06-09 13:44:58.878504
43	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #5 para 'Finalizado'.	2026-06-09 13:45:08.543989
44	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #5 para 'Finalizado'.	2026-06-09 13:45:14.686959
45	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #9 para 'Finalizado'.	2026-06-09 13:45:19.170671
46	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #5 para 'Finalizado'.	2026-06-09 13:46:25.986579
47	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #8 para 'Finalizado'.	2026-06-09 13:47:20.677432
48	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #10 para 'Finalizado'.	2026-06-09 13:47:23.370319
49	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #7 para 'Finalizado'.	2026-06-09 13:47:25.677971
50	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Finalizado'.	2026-06-09 13:47:27.740356
51	10	Inseriu um novo agendamento na esteira para o pet ID 5 em 09/06/06:45	2026-06-09 13:48:58.980435
52	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #11 para 'Finalizado'.	2026-06-09 13:49:14.778141
53	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #11 para 'Em Atendimento'.	2026-06-09 13:49:47.391938
54	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #11 para 'Agendado'.	2026-06-09 13:50:03.779363
55	10	Inseriu um novo agendamento na esteira para o pet ID 4 em 09/06/13:50	2026-06-09 13:50:28.328137
56	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #12 para 'Agendado'.	2026-06-09 13:50:35.206391
57	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #12 para 'Finalizado'.	2026-06-09 13:50:44.991422
58	10	Inseriu um novo agendamento na esteira para o pet ID 8 em 08/06/13:51	2026-06-09 13:51:46.100181
59	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #13 para 'Finalizado'.	2026-06-09 13:51:55.199867
60	10	Cadastrou o novo cliente tutor: 'teste 0906' (Email: teste@gmail).	2026-06-09 13:53:44.577985
61	10	Inseriu um novo agendamento na esteira para o pet ID 5 em 10/06/13:55	2026-06-09 13:55:15.565203
62	12	O usuário Carlos Tutor (Cliente) realizou login no sistema.	2026-06-09 13:55:45.350325
63	10	O usuário Dr. Roberto Garcia (Veterinário) realizou login no sistema.	2026-06-09 13:56:45.010281
28	\N	O usuário Jorge (Cliente) realizou login no sistema.	2026-06-09 13:30:05.680482
64	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #11 para 'Pronto para Retirada'.	2026-06-09 13:57:12.209365
65	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Pronto para Retirada'.	2026-06-09 13:57:28.011783
66	12	O usuário Carlos Tutor (Cliente) realizou login no sistema.	2026-06-09 13:57:34.201724
67	12	O usuário Carlos Tutor (Cliente) realizou login no sistema.	2026-06-09 14:00:25.12599
68	10	O usuário Dr. Roberto Garcia (Veterinário) realizou login no sistema.	2026-06-09 14:00:33.146218
69	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #11 para 'Pronto para Retirada'.	2026-06-09 14:01:21.514942
70	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #11 para 'Finalizado'.	2026-06-09 14:01:29.944374
71	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #14 para 'Finalizado'.	2026-06-09 14:02:29.131483
72	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #14 para 'Pronto para Retirada'.	2026-06-09 14:02:38.108933
73	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #14 para 'Finalizado'.	2026-06-09 14:03:12.351203
74	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Pronto para Retirada'.	2026-06-09 14:03:18.747646
75	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Em Atendimento'.	2026-06-09 14:03:27.661403
76	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #14 para 'Agendado'.	2026-06-09 14:03:39.162803
77	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #5 para 'Agendado'.	2026-06-09 14:03:52.889578
78	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #9 para 'Agendado'.	2026-06-09 14:03:55.756358
79	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #8 para 'Agendado'.	2026-06-09 14:03:57.907149
80	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #10 para 'Agendado'.	2026-06-09 14:03:59.767028
81	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #12 para 'Agendado'.	2026-06-09 14:04:03.023883
82	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #6 para 'Finalizado'.	2026-06-09 14:07:06.2799
83	10	Cadastrou o novo cliente tutor: 'Emely' (Email: emely@gmail).	2026-06-09 14:25:19.072599
84	10	Cadastrou o pet 'Nevinha' (Ursus maritimus) para o tutor de ID 16.	2026-06-09 14:27:38.471611
85	10	Cadastrou o pet 'Nevinha' (Ursus maritimus) para o tutor de ID 16.	2026-06-09 14:28:56.02156
86	10	Inseriu um novo agendamento na esteira para o pet ID 10 em 09/06/14:29	2026-06-09 14:29:45.006306
87	10	Cadastrou o pet 'Hari' (Gato) para o tutor de ID 17.	2026-06-09 14:32:45.865336
88	10	Inseriu um novo agendamento na esteira para o pet ID 11 em 09/06/14:33	2026-06-09 14:33:42.079985
89	10	Inseriu um novo agendamento na esteira para o pet ID 5 em 09/06/14:35	2026-06-09 14:35:56.046523
90	10	Inseriu um novo agendamento na esteira para o pet ID 4 em 09/06/14:36	2026-06-09 14:36:10.213894
91	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #18 para 'Em Atendimento'.	2026-06-09 14:36:23.131903
92	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #17 para 'Pronto para Retirada'.	2026-06-09 14:36:26.068568
93	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #17 para 'Finalizado'.	2026-06-09 14:36:51.635222
94	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #18 para 'Finalizado'.	2026-06-09 14:36:53.851339
95	10	Inseriu um novo agendamento na esteira para o pet ID 7 em 04/05/14:46	2026-06-09 14:46:50.09582
96	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #19 para 'Finalizado'.	2026-06-09 14:46:56.00597
97	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #19 para 'Em Atendimento'.	2026-06-09 14:47:09.485347
98	10	Inseriu um novo agendamento na esteira para o pet ID 7 em 01/06/14:47	2026-06-09 14:47:32.652344
99	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #20 para 'Finalizado'.	2026-06-09 14:47:38.869572
100	10	Inseriu um novo agendamento na esteira para o pet ID 8 em 12/05/14:47	2026-06-09 14:47:58.514953
101	10	Dr. Roberto Garcia (Veterinário) moveu o status do atendimento #21 para 'Finalizado'.	2026-06-09 14:48:04.247979
102	10	Inseriu um novo agendamento na esteira para o pet ID 4 em 09/02/14:49	2026-06-09 14:50:04.117969
103	10	Inseriu um novo agendamento na esteira para o pet ID 4 em 07/10/14:50	2026-06-09 14:50:17.755806
104	10	Cadastrou o pet 'Tubinho' (Tubarao) para o tutor de ID 16.	2026-06-09 14:53:03.051378
105	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-09 15:26:50.834202
106	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-09 16:00:06.845692
107	11	Inseriu um novo agendamento na esteira para o pet ID 7 em 09/06/16:33	2026-06-09 16:33:42.566476
108	11	Removeu o cliente de ID #16 do sistema.	2026-06-09 16:37:52.785923
109	11	Removeu o cliente de ID #15 do sistema.	2026-06-09 16:37:56.471663
110	11	Cadastrou o pet 'Nevinha' (Urso) para o tutor de ID 12.	2026-06-09 16:38:57.299377
111	11	Cadastrou o pet 'Tubinho' (Tubarao) para o tutor de ID 12.	2026-06-09 16:39:34.268498
112	11	Inseriu um novo agendamento na esteira para o pet ID 14 em 18/06/16:40	2026-06-09 16:40:14.818695
113	17	O usuário Emely (Atendente) realizou login no sistema.	2026-06-09 16:43:38.5034
114	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-09 16:44:07.632199
115	11	Inseriu um novo agendamento na esteira para o pet ID 11 em 09/06/16:44	2026-06-09 16:45:02.936762
116	11	Camila Silva Tosadora (Tosador) moveu o status do atendimento #26 para 'Em Atendimento'.	2026-06-09 16:45:11.909444
117	11	Cadastrou o pet 'Luli' (Cão) para o tutor de ID 17.	2026-06-09 16:46:34.294024
118	17	O usuário Emely (Atendente) realizou login no sistema.	2026-06-09 16:48:31.736628
119	21	O usuário Gley (Auxiliar de Veterinário) realizou login no sistema.	2026-06-09 16:50:12.703753
120	21	Inseriu um novo agendamento na esteira para o pet ID 7 em 10/06/16:51	2026-06-09 16:51:03.529942
121	21	Gley (Auxiliar de Veterinário) moveu o status do atendimento #27 para 'Pronto para Retirada'.	2026-06-09 16:51:09.325862
122	13	O usuário Evelyn (Atendente) realizou login no sistema.	2026-06-09 16:51:22.35023
123	17	O usuário Emely (Cliente) realizou login no sistema.	2026-06-09 16:58:16.074358
124	11	O usuário Camila Silva Tosadora (Tosador) realizou login no sistema.	2026-06-09 16:58:25.00818
\.


--
-- Data for Name: mural_avisos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mural_avisos (id, usuario_id, titulo, conteudo, data_publicacao, urgencia) FROM stdin;
6	11	Gabriela comeu a ração do Bolt	Vi a gabriela comendo ração escondida no estoque, demitam ela	2026-06-09 16:47:22.026702	alta
\.


--
-- Data for Name: pets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pets (id, cliente_id, nome, especie, raca, data_nascimento, comportamento, foto) FROM stdin;
4	12	pipoca	Cobra	Corn Snake	2027-01-01	Normal	pet_6a1f35ac34b188.69410212.jpg
5	12	Panda	Urso	ursidae	2026-06-04	Ansioso	pet_6a1f36163348e4.87512986.webp
7	13	Bolt	Cão	Shih tzu	2017-11-17	Requer Cuidado / Agressivo	pet_6a1f373b19c888.99756074.jpg
11	17	Hari	Gato	Gata Lacinho de Cabelo	\N	Amigável	pet_6a284e3dcd9097.69865922.jpg
13	12	Nevinha	Urso	Ursus Maritimus	2016-02-09	Amigável	pet_6a286bd145f074.09677849.jpg
14	12	Tubinho	Tubarao	SRD	2023-05-07	Normal	pet_6a286bf63d6f27.26225405.jpg
15	17	Luli	Cão	Shih tzu	2015-04-25	Amigável	default_pet.png
\.


--
-- Data for Name: servicos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.servicos (id, nome_servico, categoria, preco) FROM stdin;
2	Atendimento Clínico Exóticos	clinica	180.00
1	Banho Geral Completo	estetica	70.00
7	Tosa Higiênica	estetica	85.00
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, nome, email, senha, telefone, tipo, cargo, criado_em) FROM stdin;
10	Dr. Roberto Garcia	roberto@pethealth.com	$2y$12$3key25sToVzbrNruymm9gOYLnhzrTKWy00q8VimhISU7hP69t6q8i	11999999991	equipe	Veterinário	2026-06-02 16:51:57.395578
11	Camila Silva Tosadora	camila@pethealth.com	$2y$12$3key25sToVzbrNruymm9gOYLnhzrTKWy00q8VimhISU7hP69t6q8i	11999999992	equipe	Tosador	2026-06-02 16:51:57.395578
20	Rebecca	rebecca@gmail.com	$2y$12$dmZQ5K44Y1PCDJmpATWAPe.TCyaEbO67eGQEiGNeD/t9jpltK2Cb.	\N	equipe	Recepcionista	2026-06-09 16:24:53.808677
12	Carlos Tutor	cliente@gmail.com	$2y$12$3key25sToVzbrNruymm9gOYLnhzrTKWy00q8VimhISU7hP69t6q8i	11988888888	cliente	Cliente	2026-06-02 16:51:57.395578
13	Evelyn	evelyn@gmail.com	$2y$12$O0XhlAj6qAyOaZ/q0GQT5eqBPOv1bP5QmxOlkILGxECcjl8pS6jNW	191234567	cliente	Cliente	2026-06-02 17:01:20.52163
17	Emely	emely@gmail	$2y$12$ifEUUlvV.AuKhBQyH40.0uMSKi0rBYSXmZ.m93ssMIjOdj2Ynn5dK	123	cliente	Cliente	2026-06-09 14:25:19.062515
21	Gley	gley@gmail.com	$2y$12$sgk.1jixsG6vPI1FJVrK1O61xNdnN1SpcLaD6OlYbA.l2CvLIP76G	\N	equipe	Atendente	2026-06-09 16:32:24.233548
22	Gabriela	gabriela.machado6@pethhealt	$2y$12$qwb7aiRtAuaYWZQEuHAPu.UiBMjMpT8shkFCRZL4u0EaLtu0tq.bO	\N	equipe	Atendente	2026-06-09 16:48:11.992422
\.


--
-- Name: agendamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.agendamentos_id_seq', 27, true);


--
-- Name: log_atividades_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.log_atividades_id_seq', 124, true);


--
-- Name: mural_avisos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mural_avisos_id_seq', 6, true);


--
-- Name: pets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pets_id_seq', 15, true);


--
-- Name: servicos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.servicos_id_seq', 7, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 22, true);


--
-- Name: agendamentos agendamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT agendamentos_pkey PRIMARY KEY (id);


--
-- Name: log_atividades log_atividades_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_atividades
    ADD CONSTRAINT log_atividades_pkey PRIMARY KEY (id);


--
-- Name: mural_avisos mural_avisos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mural_avisos
    ADD CONSTRAINT mural_avisos_pkey PRIMARY KEY (id);


--
-- Name: pets pets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT pets_pkey PRIMARY KEY (id);


--
-- Name: servicos servicos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.servicos
    ADD CONSTRAINT servicos_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: pets fk_cliente; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pets
    ADD CONSTRAINT fk_cliente FOREIGN KEY (cliente_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: agendamentos fk_funcionario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT fk_funcionario FOREIGN KEY (funcionario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: agendamentos fk_pet; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT fk_pet FOREIGN KEY (pet_id) REFERENCES public.pets(id) ON DELETE CASCADE;


--
-- Name: agendamentos fk_servico; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.agendamentos
    ADD CONSTRAINT fk_servico FOREIGN KEY (servico_id) REFERENCES public.servicos(id);


--
-- Name: log_atividades fk_usuario_log; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_atividades
    ADD CONSTRAINT fk_usuario_log FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: mural_avisos fk_usuario_mural; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mural_avisos
    ADD CONSTRAINT fk_usuario_mural FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict O0twAAOhWch8mLlt0HrsXigNATc80jS83RNSoTgMca42QhaEfn2fJa75Bjgc6lL

