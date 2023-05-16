import os
from typing import List, Tuple
from dotenv import load_dotenv
load_dotenv()

from langchain.embeddings.openai import OpenAIEmbeddings
from langchain.vectorstores.pgvector import PGVector, DistanceStrategy
from langchain.docstore.document import Document
from langchain.document_loaders import PyPDFLoader
from langchain.chains.question_answering import load_qa_chain
from langchain.chat_models import ChatOpenAI

loader = PyPDFLoader('storage/app/public/documents/ZGW5WiqHcMPq8muw20IpV4qHC5MfcrhGp8aEYVLx.pdf')
pages = loader.load_and_split()

embeddings = OpenAIEmbeddings()
CONNECTION_STRING = PGVector.connection_string_from_db_params(
    driver=os.environ.get("PGVECTOR_DRIVER", "psycopg2"),
    host=os.environ.get("DB_HOST", "localhost"),
    port=int(os.environ.get("DB_PORT", "5432")),
    database=os.environ.get("DB_DATABASE", "postgres"),
    user=os.environ.get("DB_USERNAME", "postgres"),
    password=os.environ.get("DB_PASSWORD", ""),
)

db = PGVector.from_documents(
    embedding=embeddings,
    documents=pages,
    collection_name="Learn Makefiles.pdf",
    connection_string=CONNECTION_STRING,
)

print("Finish embeddings!")
