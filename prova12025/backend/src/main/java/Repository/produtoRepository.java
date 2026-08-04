package com.saep.backend.Repository;

import java.util.List;
import java.util.Optional;

import org.springframework.data.jpa.repository.JpaRepository;

import com.saep.backend.Model.Produto;
import com.saep.backend.Model.Usuario;

public interface ProdutoRepository extends JpaRepository<Produto,Long>{

   //método para buscar por partes do nome[RF]
   List<Produto> findByNomeCotainingIgnoreCase(String nome);
}