package com.saep.backend.Controller;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.saep.backend.Model.Produto;
import com.saep.backend.Repository.ProdutoRepository;

@RestController
@RequestMapping("/api/produtos")
public class ProdutoController {
    
    @Autowired
    private ProdutoRepository produtoRepository;

    //método para busca
    @GetMapping("/busca")
    public List<Produto> buscarPorNome(@RequestParam String termo){
        return produtoRepository.findByNomeCotainingIgnoreCase(termo);
    }

    @GetMapping()
    public List<Produto> getAll() {
        return produtoRepository.findAll();
    }
    

    //método para criar novo produto    
    @PostMapping
    public Produto salvar(@RequestBody Produto produto){
        if(produto.getEstoqueAtual() == null) produto.setEstoqueAtual(0);
        return produtoRepository.save(produto);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Produto> atualizar(@PathVariable Long id, @RequestBody Produto produtoAtualizado){
        return produtoRepository.findById(id).map(produto ->{
            produto.setNome(produtoAtualizado.getNome());
            produto.setDescricao(produtoAtualizado.getDescricao());
            produto.setEstoqueMinimo(produtoAtualizado.getEstoqueMinimo());
            return ResponseEntity.ok(produtoRepository.save(produto));
        }).orElseGet(()->ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deletar(@PathVariable Long id){
        produtoRepository.deleteById(id);
        return ResponseEntity.noContent().build();
    }


}
